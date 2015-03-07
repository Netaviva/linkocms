<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage module : index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Module_Controller_Index extends Linko_Controller
{
	public function main()
	{
		$aModules = Linko::Model('Module')->getModules();

        uasort($aModules, function($a, $b){
            return ($a['core'] == true || $a['core'] == 1) ? -1 : 1;
        });

        if($sAction = $this->getParam('action'))
		{
			$sModule = $this->getParam('module');
			
			// Install A module
			if($sAction == 'install')
			{
				$bInstall = true;
				
				list($bSuccess, $sMsg) = Linko::Model('Module/Action')->install($sModule);
				
				//exit;
				
				if($bSuccess)
				{
					Linko::Flash()->success($sMsg);
				}
				else
				{
					Linko::Flash()->warning($sMsg);
				}
			}
			
			// Uninstall A Module
			if($sAction == 'uninstall')
			{
				$bUnInstall = true;
				
				list($bSuccess, $sMsg) = Linko::Model('Module/Action')->uninstall($sModule);
				
				if($bSuccess)
				{
					Linko::Flash()->success($sMsg);
				}
				else
				{
					Linko::Flash()->warning($sMsg);
				}
			}
			
			// Enable A Module
			if($sAction == 'enable')
			{
				$bEnable = true;
				
				list($bSuccess, $sMsg) = Linko::Model('Module/Action')->enable($sModule);
				
				if($bSuccess)
				{
					Linko::Flash()->success($sMsg);	
				}
				else
				{
					Linko::Flash()->warning($sMsg);
				}
				
			}
			
			// Disable A Module
			if($sAction == 'disable')
			{
				$bEnable = true;
				
				list($bSuccess, $sMsg) = Linko::Model('Module/Action')->disable($sModule);
				
				if($bSuccess)
				{
					Linko::Flash()->success($sMsg);
				}
				else
				{
					Linko::Flash()->warning($sMsg);
				}
			}
			
			Linko::Response()->redirect(Linko::Url()->make('module:admincp'));
		}
		
		Linko::Template()
			->setBreadcrumb(array(
				'Modules',
			), 'Manage Modules')
            ->setTitle('Manage Modules')
			->setVars(array(
					'aModules' => $aModules,	
				)
			);
	}
}

?>