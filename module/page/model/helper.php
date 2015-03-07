<?php

class Page_Model_Helper
{
    public function getPagesForSettings()
    {
        $aPages = array();
        $aRows = Linko::Model('Page')->getPages();

        foreach($aRows as $aRow)
        {
            $aPages[$aRow['page_id']] = $aRow['page_title'];
        }

        return $aPages;
    }
}