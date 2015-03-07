<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage setting : admincp\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Setting_Controller_Admincp_Index extends Linko_Controller
{
	public function main()
	{
		$aModuleSettings = Linko::Model('Setting')->getModuleSettings();
		$aCategorySettings = Linko::Model('Setting')->getCategorySettings();
		
		Linko::Template()
			->setBreadcrumb(array(
				'Setting',
			), 'Settings')
			->setTitle('Settings')
			->setVars(array(
					'aModuleSettings' => $aModuleSettings,
					'aCategorySettings' => $aCategorySettings
				)
		);
		
		if($sCategory = $this->getParam('category'))
		{
			$bModule = false;
			
			if(substr($sCategory, 0, 7) == 'module-')
			{
				$sCategory = substr($sCategory, 7);
				$bModule = true;
			}
			else if(substr($sCategory, 0, 9) == 'category-')
			{
				$sCategory = substr($sCategory, 9);
			}

			return Linko::Module()->set('setting/admincp/edit', array(
					'sCategory' => $sCategory,
					'bModule' => $bModule,
					'aModuleSettings' => $aModuleSettings,
					'aCategorySettings' => $aCategorySettings
				)
			);
		}		
	}
}

?>