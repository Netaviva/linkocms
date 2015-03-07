<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage setting : admincp\edit.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Setting_Controller_Admincp_Edit extends Linko_Controller
{
	public function main()
	{
		$aModuleSettings = $this->getParam('aModuleSettings');
		$aCategorySettings = $this->getParam('aCategorySettings');
		$sCategory = $this->getParam('sCategory');
		$bModule = $this->getParam('bModule');

		if($bModule)
		{
			$sTitle = $sCategory;
		}
		else
		{
			$aCategory = Linko::Model('Setting')->getCategory($sCategory);
			$sTitle = $aCategory['category_title'];
		}
		
		$aSettings = Linko::Model('Setting')->getSettings($sCategory, $bModule);
		
		if($aVals = Input::post('val'))
		{
			if(Linko::Model('Setting/Action')->updateSetting($sCategory, $aVals))
			{
				Linko::Flash()->success("Settings Updated.");
				Linko::Response()->redirect('self');
			}
		}
		
		Linko::Template()
			->setBreadcrumb(array(
				'Setting' => Linko::Url()->make('setting:admincp'), 
				'Edit Settings',
			), 'Settings (' . $sTitle . ')')
			->setTitle('Settings (' . $sTitle . ')')
			->setVars(array(
					'aSettings' => $aSettings,
					'sCategory' => $sCategory,
					'aModuleSettings' => $aModuleSettings,
					'aCategorySettings' => $aCategorySettings
				)
		);		
	}
}

?>