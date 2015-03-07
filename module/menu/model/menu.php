<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage menu : model - menu.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Menu_Model_Menu extends Linko_Model
{
    private $_aLocations = array();

    public function __construct()
    {
        $this->_aLocations = array('main_menu');
    }

    /**
     * Gets all menus for a theme location
     *
     * @param string $sLocation
     * @param array $aOptions
     * @return array
     */
	public function getMenuForLocation($sLocation, $aOptions = array())
    {
        $aOptions = array_merge(array(
            'verify_allow_access' => true,
            'build_children' => true,
        ), $aOptions);

        Linko::Cache()->set(array('menu', $sLocation));

        if(!$aRows = Linko::Cache()->read())
        {
            $aRows = Linko::Database()->table('menu_item', 'mi')
                ->select('mi.*', 'm.*', 'p.page_id', 'p.page_type', 'p.page_url', 'mc.route_id')
                ->leftJoin('menu', 'm', 'm.menu_id = mi.menu_id')
                ->leftJoin('page', 'p', 'p.page_id = mi.page_id')
                ->leftJoin('module_component', 'mc', 'mc.component_id = p.component_id')
                ->where('m.menu_location', '=', $sLocation)
                ->where('mi.menu_item_status', '=', 1)
                ->group('mi.menu_item_id')
                ->order('mi.menu_item_order', 'ASC')
                ->query()
                ->fetchRows();

            Linko::Cache()->write($aRows);
        }

        $aMenus = $this->_prepareMenuItems($aRows, $aOptions['verify_allow_access'], $aOptions['build_children']);

        return $aMenus;
    }

    public function getMenu($iMenuId)
    {
        return $this->getMenus($iMenuId);
    }

    public function getMenus($iMenu = null)
    {
        $oDb = Linko::Database()->table('menu', 'm')
            ->select('m.*', 'COUNT("mi.menu_item_id") AS total_menu_item')
            ->leftJoin('menu_item', 'mi', 'mi.menu_id = m.menu_id');

        if($iMenu)
        {
            return $oDb->where('m.menu_id', '=', $iMenu)->query()
                ->fetchRow();
        }

        return $oDb->group('m.menu_id')->query()
            ->fetchRows();
    }

    public function getMenuItems($iMenuId)
    {
        $aRows = Linko::Database()->table('menu_item', 'mi')
            ->select('mi.*', 'm.*', 'p.page_id', 'p.page_type', 'p.page_url, mc.route_id')
            ->leftJoin('menu', 'm', 'm.menu_id = mi.menu_id')
            ->leftJoin('page', 'p', 'p.page_id = mi.page_id')
            ->leftJoin('module_component', 'mc', 'mc.component_id = p.component_id')
            ->where('m.menu_id', '=', $iMenuId)
            ->where('mi.menu_item_status', '=', 1)
            ->group('mi.menu_item_id')
            ->order('mi.menu_item_order', 'ASC')
            ->query()
            ->fetchRows();

        $aRows = $this->_prepareMenuItems($aRows, true, false);

        return $aRows;
    }

    public function getMenuItem($iItemId)
    {
        $aRow = Linko::Database()->table('menu_item', 'mi')
            ->select('mi.*', 'm.*')
            ->leftJoin('menu', 'm', 'm.menu_id = mi.menu_id')
            ->where('mi.menu_item_id', '=', $iItemId)
            ->query()
            ->fetchRow();

        $aRow['menu_item_param'] = $aRow['menu_item_param'] == '' ? '' : http_build_query(unserialize($aRow['menu_item_param']));

        $aRow['allow_access'] = $aRow['allow_access'] == '' ? array() : unserialize($aRow['allow_access']);

        return $aRow;
    }

    public function buildMenuRecursive($iParent, $aParents, $aItems)
    {
        $aMenus = array();

        if(isset($aParents[$iParent]))
        {
            $iCnt = 0;

            foreach($aParents[$iParent] as $iParent => $iMenu)
            {
                $aMenus[$iCnt] = $aItems[$iMenu];

                $aMenus[$iCnt]['children'] = $this->buildMenuRecursive($iMenu, $aParents, $aItems);

                $iCnt++;
            }
        }

        return $aMenus;
    }

    public function getLocations()
    {
        $aManifest = Linko::Model('Theme')->getManifest(Linko::Model('Theme')->getDefault('frontend'), 'frontend');

        return $aManifest['menu']['location'];
    }

    public function getChildren($iParent = 0)
    {
        $aRows = Linko::Database()->table('menu')
            ->select()
            ->where('parent_id', '=', $iParent)
            ->query();

        return $aRows;
    }

    public function getMenusForBlock()
    {
        $aMenus = $this->getMenus();
        $aParam = array();

        foreach($aMenus as $aMenu)
        {
            $aParam[$aMenu['menu_id']] = $aMenu['menu_title'];
        }

        return $aParam;
    }

    private function _prepareMenuItems($aRows, $bIncludeAccess = true, $bBuildChildren = true)
    {
        $aMenus = array();
        $aParents = array();

        foreach($aRows as $aRow)
        {
            $iKey = $aRow['menu_item_id'];

            $aParam = ((!empty($aRow['menu_item_param'])) ? (unserialize($aRow['menu_item_param'])) : array());

            $aAllow = ((!empty($aRow['allow_access'])) ? (unserialize($aRow['allow_access'])) : array());

            $iUserRole = Linko::Model('User/Auth')->getUserBy('role_id');

            if($bIncludeAccess)
            {
                // if the current user role is not allowed to view this menu, skipit.
                if(!in_array($iUserRole, $aAllow))
                {
                    continue;
                }
            }

            $aMenus[$iKey] = $aRow;

            // use translated menu title if it exists
            if(Linko::Language()->isTranslated('menu_translation_' . $aRow['menu_item_id']))
            {
                $aMenus[$iKey]['menu_item_title'] = Linko::Language()->translate('menu_translation_' . $aRow['menu_item_id']);
            }

            // build menu url
            if($aRow['page_id'] != NULL OR $aRow['page_id'] != 0)
            {
                if($aRow['page_type'] == 'module')
                {
                    // if page type is a module, lets use the route id
                    $aMenus[$iKey]['menu_item_url'] = Linko::Url()->make($aRow['route_id'], $aParam);
                }
                else
                {
                    // if its a user created page, use the page url/slug
                    $aMenus[$iKey]['menu_item_url'] = Linko::Url()->make($aRow['page_url'], $aParam);
                }
            }
            else
            {
                $aMenus[$iKey]['menu_item_url'] = Linko::Url()->make($aRow['menu_item_url']);
            }

            // used for building children
            $aParents[$aRow['parent_id']][] = $aRow['menu_item_id'];
        }

        if($bBuildChildren)
        {
            // rebuild menus allocating its children recursively
            $aMenus = $this->buildMenuRecursive(0, $aParents, $aMenus);
        }

        return $aMenus;
    }
}

?>