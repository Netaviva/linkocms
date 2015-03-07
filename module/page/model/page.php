<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage page : model - page.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Page_Model_Page extends Linko_Model
{
    public function init()
    {
        Linko::Model('Page')->setRoutes();
    }

	public function isHome()
	{
		return Linko::Request()->getUri() == '' ? true : false;
	}

    public function getPages($bIncludeHidden = false)
    {
        $oDb = Linko::Database()->table('page', 'p')
            ->select('p.*, mc.module_id, mc.route_id')
            ->leftJoin('module_component', 'mc', 'mc.component_id = p.component_id');
        
        if($bIncludeHidden === false)
        {
            $oDb->where('p.is_hidden', '!=', true);
        }

        $aPages = $oDb->query()->fetchRows();
       
        return $aPages;        
    }
    
	public function getPage($mSlug, $bParse = true, $bSkipCache = false)
	{
        Linko::Cache()->set(array('page', 'page_' . md5($mSlug) . ($bParse ? '_parsed' : null)));
		
		if((!$aRow = Linko::Cache()->read()) || $bSkipCache)
		{
            $aRow = Linko::Database()->table('page', 'p')
				->select('mc.component_file, mc.route_id, p.*')
                ->leftJoin('module_component', 'mc', 'p.component_id = mc.component_id AND mc.component_type = \'controller\'')
				->where((is_string($mSlug) ? "page_url = :slug" : "page_id = :id"))
				->query(array(':slug' => $mSlug, ':id' => $mSlug))
				->fetchRow();
			
			if(isset($aRow['page_id']))
			{
                $aRow['title'] = $aRow['meta_title'] ? $aRow['meta_title'] : $aRow['page_title'];
                
                // This requires the user module				
				$aRow['dissallow_access'] = $aRow['dissallow_access'] != NULL ? unserialize($aRow['dissallow_access']) : array();			
				$aRow['page_content'] = ($bParse && $aRow['page_content'] != null)  ? Linko::Shortcode()->parse($aRow['page_content']) : $aRow['page_content'];
				
                if($bSkipCache === false)
                {
                    Linko::Cache()->write($aRow);   
                }
			}
		}
        
		return $aRow;
	}

    public function getHomepage()
    {
        return Linko::Database()->table('page')
            ->select('page_url')
            ->where('is_homepage = 1')
            ->limit(1)
            ->query()
            ->fetchValue();
    }
    	
	public function getd()
	{
		$aRows = Linko::Database()->table('page', 'p')
            ->select('p.*')
			->query()->fetchRows();
		
		foreach($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['page_status'] = $aRow['page_status'] > 0 ? 'Active' : 'Hidden';	
		}
		
		return $aRows;		
	}
    
    public function setRoutes()
    {
        Linko::Cache()->set(array('page', 'routes'));
        
        if(!$aRoutes = Linko::Cache()->read())
        {
            $aRoutes = Linko::Database()->table('page', 'p')
                ->select('p.page_url', 'mc.route_id', 'mc.route_rule', 'mc.component_file', 'mc.route_id')
                ->leftJoin('module_component', 'mc', 'mc.component_id = p.component_id')
                ->where('mc.component_type', '=', 'controller')
                ->where('p.page_type', '=', 'module')
                ->query()->fetchRows();
            
            Linko::Cache()->write($aRoutes); 
        }
        
        //arr::dump($aRows);
        
        foreach($aRoutes as $aRoute)
        {
            Linko::Router()->add($aRoute['page_url'], array(
                'id' => $aRoute['route_id'],
                'controller' => $aRoute['component_file'],
                'rules' => unserialize($aRoute['route_rule']),
                'type' => Linko_Route::TYPE_PATH
            ));
        }
    }
}

?>