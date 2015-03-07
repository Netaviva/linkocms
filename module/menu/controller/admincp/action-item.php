<?php

class Menu_Controller_Admincp_Action_Item extends Linko_Controller
{
    public function main()
    {
        $sAction = $this->getParam('action');
        $iMenuId = (int)$this->getParam('menu_id');
        $iId = $this->getParam('item_id');
        $aMenuItem = array();
        $aMenu = Linko::Model('Menu')->getMenu($iMenuId);
        $aMenuItems = Linko::Model('Menu')->getMenuItems($iMenuId);
        $aPages = Linko::Model('Page')->getPages();
        $aVals = array();
        $bAdd = false;
        $bEdit = false;
        $bDelete = false;

        if($bEdit)
        {

        }

        $aParents = $this->_buildParentMenus(Linko::Model('Menu')->getMenuForLocation($aMenu['menu_location']), 0);

        Linko::Template()->setBreadcrumb(array('Manage Menus' => Linko::Url()->make('menu:admincp')))
            ->setTitle('Menus');

        switch($sAction)
        {
            case 'add':
                $bAdd = true;

                if($aVals = Input::post('val'))
                {
                    $oValidate = Linko::Validate()->set(array(
                        'title' => array('function' => 'required', 'error' => 'Must specify a menu title.')
                    ));

                    if($oValidate->isValid($aVals))
                    {
                        $aVals['menu_id'] = $aMenu['menu_id'];

                        if(Linko::Model('Menu/Action')->addMenuItem($aVals))
                        {
                            Linko::Flash()->success('Menu Successfully Created.');
                            Linko::Response()->redirect(Linko::Url()->make('menu:admincp', array('menu_id' => $aVals['menu_id'])));
                        }
                    }
                }

                Linko::Template()->setBreadcrumb(array('Add Menu'), 'Add Menu Link')
                    ->setTitle('Add Menu Link');

                break;
            case 'edit':

                $bEdit = true;

                $aMenuItem = Linko::Model('Menu')->getMenuItem($iId);

                Linko::Template()->setBreadcrumb(array('Edit Menu Item'), 'Edit Menu Link')
                    ->setTitle('Edit Menu Link');

                if($aVals = Input::post('val'))
                {
                    $oValidate = Linko::Validate()->set(array(
                        'title' => array('function' => 'required', 'error' => 'Must specify a menu title.')
                    ));

                    if($oValidate->isValid($aVals))
                    {
                        if(Linko::Model('Menu/Action')->updateMenuItem($iId, $aVals))
                        {
                            Linko::Flash()->success('Menu Successfully Updated.');
                            Linko::Response()->redirect(Linko::Url()->make('menu:admincp', array('menu_id' => $iMenuId)));
                        }
                    }
                }

                break;
            case 'delete':
                $bDelete = true;

                if($iId && (Linko::Model('Menu/Action')->deleteMenuItem($iId)))
                {
                    Linko::Flash()->success('Menu Successfully Deleted.');
                    Linko::Response()->redirect(Linko::Url()->make('menu:admincp', array('menu_id' => $iMenuId)));
                }
                break;
        }

        Linko::Template()->setVars(array(
            'aUserRoles' => Linko::Model('User/Role')->getRoles(),
            'aParents' => $aParents,
            'iMenuId' => $iId,
            'bAdd' => $bAdd,
            'bDelete' => $bDelete,
            'bEdit' => $bEdit,
            'aMenuItem' => $aMenuItem,
            'aPages' => $aPages,
            'aVals' => $aVals
        ));
    }

    private function _buildParentMenus($aItems, $iDepth = 0, $iSkipId = null)
    {
        $aMenus = array();

        $iCnt = 0;

        foreach($aItems as $aItem)
        {
            if($iSkipId && ($aItem['menu_item_id'] == $iSkipId))
            {
                //continue;
            }

            $iCnt++;

            $aMenus[] = array(
                'menu_item_id' => $aItem['menu_item_id'],
                'menu_item_title' => $aItem['menu_item_title'],
                'parent_id' => $aItem['parent_id'],
                'menu_depth' => $iDepth,
            );

            if(count($aItem['children']))
            {
                $aMenus = array_merge($aMenus, $this->_buildParentMenus($aItem['children'], $iDepth + 1));
            }
        }

        return $aMenus;
    }
}