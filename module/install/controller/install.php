<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage install : install.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Install_Controller_Install extends Linko_Controller
{
	/**
	 * @var Install_Model_Install
	 */
	private $_oInstall;
    
    public function main()
    {
        $this->_oInstall = Linko::Model('install');

        $this->_oInstall->run($this);

        if($this->_oInstall->isInstalled() && ($this->_oInstall->getStep() != 'finish'))
        {
            exit('Nooooh! Can\'t do this');
        }

        Linko::Template()
            ->setLayoutDirectory('module_install')
            ->setType(null)
            ->setLayout('install')
            ->setTitle('linkoCMS Installer')
            ->clearStyle()
            ->setHeader(Html::script('var Config = ({base_url: \'' . Linko::Url()->path() . '\', asset_image: \'' . Linko::Url()->path('asset/image') . '\', ajax_url: \'' . Linko::Url()->path('asset') . 'ajax.php\'});'
            ))
            ->setStyle(array('linko.css'), 'asset_css')
            ->setStyle(array(
                'foundation.min.css', 
                'install.css'
            ), 'module_install')
            ->setScript(array(
                'jquery/jquery-1.8.3.js',
                'linko.js',
                'foundation/modernizr.foundation.js', 
                'foundation/foundation.js'     
            ), 'asset_js')
            ->setScript('install.js', 'module_install')
            ->setVars(array(
                'aSteps' => Linko::Model('install')->getSteps(),
                'sCurrentStep' => Linko::Model('install')->getStep(),
                'sLayout' => ('step' . DS . $this->_oInstall->getStep()),
            ));
    }
    
    public function _step_license()
    {
        if(Input::post('agree-license'))
        {
            $this->_oInstall->gotoStep($this->_oInstall->getNextStep());
        }
    }
    
    public function _step_precheck()
    {
        $aRequirements = $this->_oInstall->getRequirement();
        $aPhpSettings = $this->_oInstall->getPhpSettings();
        
        $iFailed = 0;
        
        foreach($aRequirements as $aRequirement)
        {
            if($aRequirement['value'] == false)
            {
                $iFailed++;
            }
        }
        
        if($iFailed == 0 && Input::post('proceed'))
        {
            $this->_oInstall->gotoStep($this->_oInstall->getNextStep());
        }

	    $aDirectories = array();

        $sApplication = INCLUDE_DIR . 'config' . DS . 'application.php';

        $aDirectories[$sApplication] = (Dir::isWritable($sApplication) ? 'passed' : 'failed');

	    foreach(Dir::getFolders(Linko::Config()->get('dir.storage')) as $sDir)
	    {
		    $aDirectories[$sDir] = (Dir::isWritable($sDir) ? 'passed' : 'failed');
	    }
        
        Linko::Template()->setVars(array(
            'aRequirements' => $aRequirements,
            'aPhpSettings' => $aPhpSettings,
	        'aDirs' => $aDirectories,
            'iFailed' => $iFailed
        ));
    }
    
    public function _step_configuration()
    {
        if($aDbVals = Input::post('db'))
        {
			$this->_oInstall->writeDatabaseConfig($aDbVals);
        }

	    if($aModules = Input::post('module'))
	    {
		    $this->_oInstall->setOption('install_modules', $aModules);

		    $this->_oInstall->gotoStep($this->_oInstall->getNextStep());
	    }

	    $this->_oInstall->removeOption('install_tables');
	    $this->_oInstall->removeOption('install_modules');

	    Linko::Template()->setVars(array(
		    'aModules' => Linko::Model('Module')->getModules(false)
	    ));
    }

	public function _step_process()
	{
		$aModules = (array)$this->_oInstall->getOption('install_modules');

		$aInstalled = array();
		$bTable = false;
		$bComplete = false;

		$iCnt = 0;
		$iLimit = 13;

		if(!$this->_oInstall->getOption('install_tables'))
		{
			$bTable = true;

			//Install Tables
			foreach($aModules as $sModule)
			{
				$aManifest = Linko::Model('Module')->getManifest($sModule);

				if(isset($aManifest['table']))
				{
					$aTables = unserialize(trim($aManifest['table']));

					foreach($aTables as $sTable => $aFields)
					{
						Linko::Database()->table($sTable)->create($aFields, true)->query();
					}
				}
			}

			$this->_oInstall->setOption('install_tables', '1');
		}
		else
		{
			// Install Modules
			foreach($aModules as $sModule)
			{
				$iCnt++;

				if($iCnt > $iLimit)
				{
					break;
				}

				list($bPass, $sError) = Linko::Model('Module/Action')->install($sModule);

				if($bPass)
				{
					$aInstalled[] = $sModule;
				}
				else
				{
					Linko::Error()->set($sError);
				}
			}

			$this->_oInstall->removeOption('install_modules');

			$aDiff = array_diff($aModules, $aInstalled);

			if(count($aDiff))
			{
				$this->_oInstall->setOption('install_modules', $aDiff);
			}

			if(!count($aInstalled) && !count($aDiff))
			{
				$this->_oInstall->gotoStep($this->_oInstall->getNextStep());
			}
		}

		Linko::Template()->setVars(array(
			'aInstalled' => $aInstalled,
			'bTable' => $bTable
		));
	}

	public function _step_finalize()
	{
		if(($aUser = Input::post('user')) && ($aSetting = Input::post('sett')))
		{
			$aUser = array_merge(array(
				'role_id' => CMS::USER_ROLE_ADMIN,
                'activated' => true
			), $aUser);

			// Add Administrator
			list($iUser, $aUser) = Linko::Model('User/Action')->add($aUser);

			// Update General Site Setting
			Linko::Model('Setting/Action')->updateModuleSetting('page', $aSetting);

			// Creates a sample homepage
            $iHomePageId = Linko::Model('Page/Action')->add(array(
                'page_title' => 'Sample Page',
                'page_content' => File::read(DIR_MODULE . 'install' . DS . 'layout' . DS . 'sample' . DS . 'sample-page.html'),
                'page_status' => 1,
                'page_url' => '',
                'meta_title' => 'Sample Page',
                'meta_keywords' => 'sample, page, linkocms, linkodev',
                'meta_description' => 'sample page linkocms. Developed by linkodev.',
                'component_id' => 0,
            ));

            // Set the sample page as home page
            Linko::Model('Page/Action')->setHomepage($iHomePageId);

			// Set default theme for frontend and backend
			Linko::Model('Theme')->setDefault('default', 'frontend');
            Linko::Model('Theme')->setDefault('default', 'backend');

            // Get page id for user login
            $iUserloginPage = Linko::Database()->table('page')
                ->select('page_id')
                ->where('component_id', '=', (Linko::Database()->table('module_component')
                    ->select('component_id')
                    ->where('route_id', '=', 'user:login'))
            )
            ->query()
            ->fetchValue();

            // Get page id for user login
            $iUserlogoutPage = Linko::Database()->table('page')
                ->select('page_id')
                ->where('component_id', '=', (Linko::Database()->table('module_component')
                    ->select('component_id')
                    ->where('route_id', '=', 'user:logout'))
            )
            ->query()
            ->fetchValue();

            // Get page id for browse members
            $iUserPage = Linko::Database()->table('page')
                ->select('page_id')
                ->where('component_id', '=', (Linko::Database()->table('module_component')
                    ->select('component_id')
                    ->where('route_id', '=', 'user:browse'))
            )
            ->query()
            ->fetchValue();

            $iMainMenu = Linko::Model('Menu/Action')->addMenu(array(
                'title' => 'Main Menu',
                'location' => 'main_menu'
            ));

            // Add Home Menu
            Linko::Model('Menu/Action')->addMenuItem(array(
                'title' => 'Sample Page',
                'url' => '',
                'menu_id' => $iMainMenu,
                'page_id' => $iHomePageId,
                'status' => 1,
                'allow_access' => array(
                    CMS::USER_ROLE_ADMIN,
                    CMS::USER_ROLE_USER,
                    CMS::USER_ROLE_GUEST
                )
            ));

            // Add User browse to menu
            Linko::Model('Menu/Action')->addMenuItem(array(
                'title' => 'Browse Members',
                'menu_id' => $iMainMenu,
                'page_id' => $iUserPage,
                'status' => 1,
                'allow_access' => array(
                    CMS::USER_ROLE_ADMIN,
                    CMS::USER_ROLE_USER
                )
            ));

            // Add User login to menu
            Linko::Model('Menu/Action')->addMenuItem(array(
                'title' => 'Login',
                'menu_id' => $iMainMenu,
                'page_id' => $iUserloginPage,
                'status' => 1,
                'allow_access' => array(
                    CMS::USER_ROLE_GUEST
                )
            ));

            // Add User logout to menu
            Linko::Model('Menu/Action')->addMenuItem(array(
                'title' => 'Logout',
                'menu_id' => $iMainMenu,
                'page_id' => $iUserlogoutPage,
                'status' => 1,
                'allow_access' => array(
                    CMS::USER_ROLE_ADMIN,
                    CMS::USER_ROLE_USER
                )
            ));

            Linko::Model('Menu/Action')->addMenuItem(array(
                'title' => 'Administrator',
                'menu_id' => $iMainMenu,
                'url' => 'admincp',
                'status' => 1,
                'allow_access' => array(
                    CMS::USER_ROLE_ADMIN
                )
            ));

            // Assign blocks
            /**Linko::Model('Block/Action')->assignBlock(array(

            ));**/

            $sFile = INCLUDE_DIR . 'config' . DS . 'application.php';

            $iUMask = umask(0);
            @chmod($sFile, 0777);

            $sContent = preg_replace("/[\"|']application\.installed[\"|'],\s+(?:true|false)\s+/i", "'application.installed', true", File::read($sFile));

			File::write($sFile, $sContent, null, true);
            umask($iUMask);

			$this->_oInstall->clearOptions();

			$this->_oInstall->gotoStep($this->_oInstall->getNextStep());
		}
	}

	public function _step_finish()
	{

	}
}

?>