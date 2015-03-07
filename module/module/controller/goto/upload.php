<?php

class Module_Controller_Goto_Upload extends Linko_Controller
{
	public function main()
	{
		$bModulePage = false;
		$aManifest = array();
		$aInstallInfo = array();
		$aUpgradeModule = array();
		$aModule = array();
		$bFail = false;
		$sModule = null;

		$bIsUpgrade = false;

		if(Input::post('upload'))
		{
			$oUpload = Linko::Upload()->setAllowedType('zip')
				->setDestination(DIR_TMP);

			$oUpload->load('package');

			if($oUpload->isUploaded())
			{
				$aFiles = Linko::Pclzip($oUpload->getFile())->extract(PCLZIP_OPT_PATH, DIR_TMP . 'module' . DS);

				$sModule = isset($aFiles[0]['stored_filename']) ? substr($aFiles[0]['stored_filename'], 0, strpos($aFiles[0]['stored_filename'], '/')) : null;

                $sPath = DIR_TMP . 'module' . DS . $sModule . DS;

				if($sPath)
				{
					if(!File::exists($sPath . 'manifest.xml'))
					{

						Dir::delete(DIR_TMP . 'module' . DS,  true);
						Linko::Flash()->warning('Invalid Module Package!');
						Linko::Response()->redirect('module:admincp');
					}

					$aModule = Linko::Model('Module')->readManifest($sPath . 'manifest.xml');

					$bModulePage = true;

					list($aInstallInfo, $bFail) = Linko::Model('Module')->getInstallInfo($sPath);

					if(Dir::exists(DIR_MODULE . $sModule))
					{
						$bIsUpgrade = true;
						$aUpgradeModule = Linko::Model('Module')->getManifest($sModule);
					}
				}
			}
		}

        // install
		if(Input::post('install') && $bFail == false)
		{
			$sModule = Input::post('module');

			Dir::move(DIR_TMP . 'module' . DS . $sModule, DIR_MODULE . $sModule . DS);

			Linko::Model('Module/Action')->install($sModule);

			Linko::Flash()->success('Module Successfully Installed.');
			Linko::Response()->redirect('module:admincp');
		}

        // upgrade
		if(Input::post('upgrade') && $bFail == false)
		{
			$sModule = Input::post('module');

            $aUpgradeModule = Linko::Model('Module')->getManifest($sModule);

			Dir::move(DIR_TMP . 'module' . DS . $sModule, DIR_MODULE . $sModule . DS);

			list($bRet, $sMessage) = Linko::Model('Module/Action')->upgrade($sModule, $aUpgradeModule['version']);

			Linko::Flash()->success($sMessage);
			Linko::Response()->redirect('module:admincp');
		}

		Linko::Template()->setBreadcrumb(array(), 'Upload Module Package')
			->setTitle('Install Module &raquo; Upload Module Package')
			->setVars(array(
				'bIsUpgrade' => $bIsUpgrade,
				'aUpgradeModule' => $aUpgradeModule,
				'bModulePage' => $bModulePage,
				'aInstallInfo' => $aInstallInfo,
				'aModule' => $aModule,
				'bFail' => $bFail,
				'sModule' => $sModule
			), $this);

	}

	private function _clear($sPath)
	{

	}
}