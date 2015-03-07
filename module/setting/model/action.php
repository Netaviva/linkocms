<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage setting : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Setting_Model_Action extends Linko_Model
{
    public function add($sModule, $aVals)
    {
	    if(!Arr::hasKeys($aVals, 'var'))
	    {
		    return false;
	    }

	    $aVals = array_merge(array(
		    'type' => 'string',
		    'value' => '',
		    'title' => Inflector::humanize($aVals['var']),
		    'description' => Inflector::humanize($aVals['var'])
	    ), $aVals);

        $aVals['data'] = isset($aVals['data']) ? $aVals['data'] : '';

        $aParam = array(
            'data' => $aVals['data']
        );

        $aInsert = array(
            'module_id' => $sModule,
            'setting_var' => $aVals['var'],
            'setting_type' => $aVals['type'],
            'setting_value' => $aVals['value'],
            'setting_value_default' => $aVals['value'],
            'setting_title' => $aVals['title'],
            'setting_description' => $aVals['description'],
            'setting_param' => serialize($aParam)
		);

        $iId = Linko::Database()->table('setting')
	        ->insert($aInsert)
	        ->query()
	        ->getInsertId();

	    return $iId;
    }

	public function addSettings($sModule, $aVals)
	{
		$aInsert = array();
		$aIds = array();

		foreach($aVals as $aVal)
		{
            // var, type, value, title, description [, data]
			if(!Arr::hasKeys($aVal, 'var'))
			{
				continue;
			}

			$aVal = array_merge(array(
				'type' => 'string',
				'value' => '',
				'title' => Inflector::humanize($aVal['var']),
				'description' => Inflector::humanize($aVal['var'])
			), $aVal);


			$aVal['data'] = isset($aVal['data']) ? $aVal['data'] : '';

			$aParam = array(
				'data' => $aVal['data']
			);

			$aInsert[] = array(
                $sModule,
                $aVal['var'],
                $aVal['type'],
                $aVal['value'],
                $aVal['value'],
                $aVal['title'],
                $aVal['description'],
                serialize($aParam)
			);
		}

		return Linko::Database()->table('setting')
             ->insert(array(
                'module_id',
                'setting_var',
                'setting_type',
                'setting_value',
                'setting_value_default',
                'setting_title',
                'setting_description',
                'setting_param'), $aInsert)
            ->query()
            ->getAffectedRows();
	}

	public function updateSetting($sCategory, $aVals, $bModule = false)
	{
		if(!isset($aVals['value']))
		{
			return true;	
		}
		
		$aUpdate = array();
		
		foreach($aVals['value'] as $sKey => $mValue)
		{
			if(is_array($mValue))
			{
				
			}
			else
			{
				$aUpdate['setting_value'] = $mValue;
			}
			
			Linko::Database()->table('setting')
                ->update($aUpdate)
				->where("setting_var = :key" . ($bModule ? " AND module = :module" : ''))
				->query(array(':key' => $sKey, ':module' => $sCategory));
		}
		
        Linko::Cache()->delete(array('setting', 'settings'));
        
		return true;		
	}

    public function deleteSetting($sModule, $sVar)
    {
        Linko::Database()->table('setting')
            ->delete()
            ->where('setting_var', '=', $sVar)
            ->where('module_id', '=', $sModule)
            ->query();

        return true;
    }
	
	public function updateModuleSetting($sModule, $aVals)
	{
		foreach($aVals as $sKey => $mValue)
		{
			$aUpdate = array();

			if(is_array($mValue))
			{
				
			}
			else
			{
				$aUpdate['setting_value'] = $mValue;
			}
			
			Linko::Database()->table('setting')
                ->update($aUpdate)
                ->where('setting_var', '=', $sKey)
				->where('module_id', '=', $sModule)
                ->query();
		}
		
		return true;		
	}

	public function addCategory($sTitle)
	{
		if($sTitle)
		{
			$iId = Linko::Database()->table('setting_category')
				->insert(array('category_title' => $sTitle))
				->query()
				->getInsertId();

			return $iId;
		}

		return false;
	}

	public function setSettingCategory($iCategory, $mSettingId)
	{
		if(!is_array($mSettingId))
		{
			$mSettingId = array($mSettingId);
		}

		foreach($mSettingId as $iId)
		{
			Linko::Database()->table('setting')
				->update(array('category_id' => $iCategory))
				->where('setting_id', '=', $iId)
				->query();
		}

		return true;
	}

}

?>