<?php

/**
 * @package Mobile Module
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class Mobile_Model_Mobile extends Linko_Model
{
    /**
     * Gets all installed modules and
     * pages associated with the modules.
     */
    public function getModules()
    {
        /** @var $oModule Module_Model_Module*/
        $oModule = Linko::Model('Module');

        /** @var $oPage Page_Model_Page*/
        $oPage = Linko::Model('Page');

        $aModules = $oModule->getModules();
        $aPages = $oPage->getPages();

        // Here, we get the pages and add 'pages' key with the pages
        // to the array of modules that have pages.
        foreach($aPages as $aPage)
        {
            if(!empty($aPage['module_id']))
            {
                $aModules[$aPage['module_id']]['pages'][] = $aPage;
            }
        }

        // Filter off the modules without pages controller.
        // We are then left with only modules that have pages.
        $aModules = array_filter($aModules, function($aModule){
            return isset($aModule['pages']) !== false;
        });

        return $aModules;
    }

    /**
     * Gets a dashboard item
     *
     * @param int $iId
     *
     * @return array|bool
     */
    public function getDashboardItem($iId)
    {
        $sCache = Linko::Cache()->set(array('mobile', 'dashboard_item_' . $iId));

        if(!$aRow = Linko::Cache()->read($sCache))
        {
            $aRow = $this->_getDashboardQuery()
                ->where('item_id', '=', $iId)
                ->query()
                ->fetchRow();

            $aRow['item_image'] = (($aRow['item_image'] != '') ?: Linko::Template()->getImage('module/toolbar.png', 'module_mobile'));
            $aRow['item_url'] = Linko::Url()->make(($aRow['route_id']) ? $aRow['route_id'] : $aRow['page_url']);

            Linko::Cache()->write($aRow, $sCache);
        }

        return $aRow;
    }

    /**
     * Gets all dashboard items.
     */
    public function getDashboardItems()
    {
        $sCache = Linko::Cache()->set(array('mobile', 'dashboard_items'));

        if(!$aRows = Linko::Cache()->read($sCache))
        {
            $aRows = $this->_getDashboardQuery()
                ->order('item_order', 'DESC')
                ->query()
                ->fetchRows();

            foreach($aRows as $iKey => $aRow)
            {
                $aRows[$iKey]['item_image'] = (($aRow['item_image']) ?: Linko::Template()->getImage('module/toolbar.png', 'module_mobile'));
                $aRows[$iKey]['item_url'] = Linko::Url()->make(($aRow['route_id']) ? $aRow['route_id'] : $aRow['page_url']);
            }

            Linko::Cache()->write($aRows, $sCache);
        }

        return $aRows;
    }

    /**
     * @return Linko_Database_Sql_Query_Builder
     */
    private function _getDashboardQuery()
    {
        return Linko::Database()->table('mobile_dashboard', 'md')
            ->select('md.*', 'p.page_title', 'p.page_url', 'mc.module_id', 'mc.route_id')
            ->leftJoin('page', 'p', 'p.page_id = md.page_id')
            ->leftJoin('module_component', 'mc', 'mc.component_id = p.component_id');
    }
}