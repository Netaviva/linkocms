<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage page : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Page_Model_Action extends Linko_Model
{
	// Add/Edit a Page
	public function add($aVals, $iId = false)
	{
		$bEdit = $iId ? true : false;

        $sDissallow = null;

        if(isset($aVals['dissallow_access']))
        {
            $sDissallow = serialize($aVals['dissallow_access']);
        }

		// Lets check for duplicate
		$bRow = Linko::Database()->table('page')
			->select('page_id')
			->where("page_url = :url" . ($bEdit ? " AND page_id != :id" : ''))
			->query(array(':url' => $aVals['page_url'], ':id' => $iId))
            ->getCount();
		
		if($bRow)
		{
			Linko::Error()->set("There seems to be a page with the same url.");
			
			return false;	
		}

        $aVals = array_merge(array(
            'meta_keywords' => '',
            'meta_description' => '',
            'page_status' => 0,
            'meta_title' => '',
            'component_id' => null,
            'page_type' => 'content'
        ), $aVals);

		$aData = array(
			'page_title' => $aVals['page_title'],
			'page_content' => isset($aVals['page_content']) ? $aVals['page_content'] : NULL, // module page dont have custom content
			'page_status' => (boolean)$aVals['page_status'],
			'meta_title' => $aVals['meta_title'],
			'meta_keywords' => $aVals['meta_keywords'],
			'meta_description' => $aVals['meta_description'],
			'dissallow_access' => $sDissallow,
            'page_url' => Inflector::underscore($aVals['page_url']),
            'component_id' => $aVals['component_id'],
            'page_type' => $aVals['page_type']
		);

        // set Hompage
        if(isset($aVals['is_homepage']))
        {
            $this->setHomepage($iId);
        }

        // set Layout
        if(isset($aVals['layout']))
        {
            $this->setLayout($iId, $aVals['layout']);
        }

		if($bEdit)
		{
			$aData['time_updated'] = Date::now();
			
			Linko::Database()->table('page')
				->update($aData)
				->where('page_id', '=', $iId)
				->query();

            Linko::Plugin()->call('page.update_page', $iId, $aData);

            Linko::Cache()->delete('page', 'dir');

            return $iId;
		}
		else
		{
			$aData['time_created'] = Date::now();
            $aData['time_updated'] = Date::now();
			
			$iId = Linko::Database()->table('page')
				->insert($aData)
				->query()
                ->getInsertId();

            if(isset($aVals['add_menu']))
            {
                $this->setHomepage($iId);
            }

            Linko::Plugin()->call('page.add_page', $iId, $aData);

            Linko::Cache()->delete('page', 'dir');

            return $iId;
		}
        
		return false;
	}
	
	// edit a page ... see add()
	public function edit($iId, $aVals)
	{
		return $this->add($aVals, $iId);	
	}
	
	// delete a page
	public function delete($iId)
	{
		Linko::Database()->table('page')
			->delete()
			->where('page_id', '=', $iId)
			->query();
		
		return true;
	}
    
    public function setHomepage($iId)
    {
        // Set all to zero
        Linko::Database()->table('page')
            ->update(array('is_homepage' => 0))
            ->query();
        
        // Set this page
        Linko::Database()->table('page')
            ->update(array('is_homepage' => 1))
            ->where("page_id = :id")
            ->query(array(':id' => $iId));
        
        return true;
    }

    public function setLayout($iPage, $sLayout, $sType = 'frontend')
    {
        // if the layout is not valid or does not exists, set it to null
        if(!Linko::Model('Theme')->isLayout($sLayout, $sType))
        {
            $sLayout = null;
        }

        Linko::Database()->table('page')
            ->update(array('page_layout' => $sLayout))
            ->where("page_id = :id")
            ->query(array(':id' => $iPage));

        return true;
    }
}

?>