<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage setting : model - setting.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Setting_Model_Setting extends Linko_Model
{
    public function init()
    {
        Linko::Cache()->set(array('setting', 'settings'));
        
        if(!$aSetting = Linko::Cache()->read())
        {
            $aSetting = array();
            
            $aRows = Linko::Database()->table('setting')
                    ->select('setting_id, module_id, setting_var, setting_type, setting_value, setting_value_default')
    				->query()
                    ->fetchRows(); 

            foreach($aRows as $aRow)
			{
				$sVar = $aRow['setting_var'];
                
				$aSetting[$sVar] = Linko::Module()->setSettingType($aRow['setting_value'], $aRow['setting_type']);	
			}
            
            Linko::Cache()->write($aSetting); 
        }

        Linko::Module()->addSetting($aSetting);       
    }

	public function getModuleSettings()
	{
		return Linko::Database()
			->table('setting')
			->select('module_id, COUNT("module_id") AS total_module_setting')
			->group('module_id')
			->where("module_id != ''")
			->query()->fetchRows();
	}

	public function getCategorySettings()
	{
		return Linko::Database()->table('setting_category', 'sc')
			->select('sc.category_id, sc.category_slug, sc.category_title, COUNT(s.category_id) AS total_category_setting')
			->leftJoin('setting', 's', 's.category_id = sc.category_id')
			->group('sc.category_title')
			->query()->fetchRows();
	}
    
	public function getSettings($sCategory, $bModule = false)
	{
		$sCond = $bModule ? "module_id = :category" : "category_id = :category";
		
		$aRows = Linko::Database()->table('setting')
            ->select('*')
			->where($sCond)
			->query(array(':category' => $sCategory))->fetchRows();
		
		foreach($aRows as $iKey => $aRow)
		{
            $aRows[$iKey]['setting_param'] = $aRow['setting_param'] ? unserialize($aRow['setting_param']) : array();
			$aRows[$iKey]['setting_value'] = Linko::Module()->setSettingType($aRow['setting_value'], $aRow['setting_type']);	
		}
		
		if(!is_array($aRows))
		{
			return array();	
		}
		
		return $aRows;
	}

	public function getCategory($iId)
	{
		return Linko::Database()->table('setting_category')
			->select()
			->where('category_id', '=', $iId)
			->query()
			->fetchRow();
	}
}

?>