<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage module : model - module.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Module_Model_Module extends Linko_Model
{
    private $_aModules = array();
    
    public function init()
    {
        $aModules = array_filter($this->getModules(), function($aModule)
        {
            return $aModule['enabled'] == true;
        });
        
        foreach($aModules as $sModule => $aModule)
        {
            Linko::Module()->add($sModule, $aModule);
        }
    }
    
	public function getModules($bInstall = true)
	{
        $sCache = Linko::Cache()->set(array('extension', 'modules' . ($bInstall ? '_install' : '')));
        
        if(!$this->_aModules = Linko::Cache()->read($sCache))
        {
	        $aInstalled = array();

	        if($bInstall)
	        {
	            // Get all installed modules
	            $aRows = Linko::Database()->table('module')
	                ->select()
	                ->query()
	                ->fetchRows();

	            foreach($aRows as $aRow)
	            {
	                $aInstalled[$aRow['module_id']] = $aRow;
	            }
	        }

    		// Browse through the module folders
    		foreach(Dir::getFolders(Linko::Config()->get('dir.module')) as $sDirectory)
    		{
    			$sModule = strtolower(basename($sDirectory));
    			
    			if(!$aManifest = $this->getManifest($sModule))
    			{
    				continue;	
    			}
    					
    			// set details for each module. Both installed and not installed.
    			$this->_aModules[$sModule] = array(
    				'module_id' => $sModule,
    				'title' => $aManifest['title'],
    				'version' => $aManifest['version'],
    				'enabled' => isset($aInstalled[$sModule]['enabled']) ? (bool)$aInstalled[$sModule]['enabled'] : false,
    				'description' => $aManifest['description'],
    				'installed' => isset($aInstalled[$sModule]) ? true : false,
    				'core' => $aManifest['core']
    			);
    		}
            
            Linko::Cache()->write($this->_aModules, $sCache);   
        }
			
		return $this->_aModules;	
	}
    
    public function isModule($sModule)
    {
        return array_key_exists($sModule, $this->_aModules);
    }
    
    public function isEnabled($sModule)
    {
        return ($this->isModule($sModule) && ($this->_aModules[$sModule]['enabled']));
    }
    
    public function isInstalled($sModule)
    {
        return ($this->isModule($sModule) && ($this->_aModules[$sModule]['installed']));
    }
    
	public function getManifest($sModule)
	{
		$sFile = Linko::Config()->get('dir.module') . $sModule . DS . 'manifest.xml';

		$aManifest = $this->readManifest($sFile);

		return $aManifest;			
	}

	public function readManifest($sFile)
	{
		if(!File::exists($sFile))
		{
			return array();
		}

		$aManifest = array_merge(array(
			'title' => basename(dirname($sFile)),
			'version' => '1.0.0',
			'description' => 'None Avaialable',
			'core' => isset($aManifest['core']) ? $aManifest['core'] : false,
			'author' => null,
			'author_url' => null,
			'core' => 0,
			'auto_enable' => 0,
			'component' => array(),
			'requirement' => array(),
            'settings' => array(),
		), Linko::Xml()->parse($sFile));

		$aManifest['component'] = array_merge(array(
			'controller' => array(),
			'block' => array(),
		), $aManifest['component']);

		$aManifest['requirement'] = array_merge(array(
			'module' => array()
		), $aManifest['requirement']);

        $aManifest['settings'] = array_merge(array(
            'setting' => array()
        ), $aManifest['settings']);

		if(count($aManifest['requirement']['module']) && !isset($aManifest['requirement']['module'][0]))
		{
			$aManifest['requirement']['module'] = array($aManifest['requirement']['module']);
		}

		if(count($aManifest['component']['block']) && !isset($aManifest['component']['block'][1]))
		{
			$aManifest['component']['block'] = array($aManifest['component']['block']);
		}

        if(count($aManifest['settings']['setting']) && !isset($aManifest['settings']['setting'][1]))
        {
            $aManifest['settings']['setting'] = array($aManifest['settings']['setting']);
        }

		return $aManifest;
	}

	public function getModuleInfo($sModule)
	{
		$aManifest = $this->getManifest($sModule);

		return $this->buildModuleInfo($aManifest);
	}

	public function getModuleInfoFromPath($sPath)
	{
		$aManifest = $this->readManifest(rtrim($sPath, '/ ' . DS) . DS . 'manifest.xml');
		$sModule = trim(basename($sPath), '/ ' . DS);

		return $this->buildModuleInfo($aManifest);
	}

	public function buildModuleInfo($aManifest)
	{
		$aGeneral = array(
			'title' => $aManifest['title'],
			'version' => $aManifest['version'],
			'description' => $aManifest['description'],
			'is_core' => (bool)$aManifest['core'],
			'auto_enable' => (bool)$aManifest['auto_enable'],
			'author' => $aManifest['author']
		);

		$aRequirements = array();

		$iCnt = 0;

		foreach($aManifest['requirement']['module'] as $aRequirement)
		{
			if(!Arr::hasKeys($aRequirement, 'id'))
			{
				continue;
			}

			$iCnt++;

			$aRequirements[$iCnt] = array_merge($aRequirement, array('type' => 'module', 'module_pass' => false));

			if(isset($aRequirement['id']) && Linko::Module()->isModule($aRequirement['id']))
			{
				$aRequirements[$iCnt]['module_pass'] = true;
			}

			if(isset($aRequirement['version']))
			{
				$bVersionPass = false;

				if(Dir::exists(DIR_MODULE . $aRequirement['id']))
				{
					$aModuleManifest = $this->getManifest($aRequirement['id']);

					if(version_compare($aModuleManifest['version'], $aRequirement['version']) >= 0)
					{
						$bVersionPass = true;
					}
					else
					{
						$bVersionPass = false;
					}
				}

				$aRequirements[$iCnt]['version_pass'] = $bVersionPass;
			}
		}

		return array('general' => $aGeneral, 'requirement' => $aRequirements);
	}

	public function getInstallInfo($sModule)
	{
		$aModuleInfo = $this->getModuleInfoFromPath($sModule);

		$iFailed = 0;

		foreach($aModuleInfo['requirement'] as $aRequirement)
		{
			if(!isset($aRequirement['optional']) && ($aRequirement['module_pass'] == false || (isset($aRequirement['version_pass']) && $aRequirement['version_pass'] == false)))
			{
				$iFailed++;
			}
		}

		return array($aModuleInfo, $iFailed);
	}

    public function getModuleControllers($sModule)
    {
        return $this->getModuleComponent($sModule, 'controller');
    }

    public function getModuleBlocks($sModule)
    {
        return $this->getModuleComponent($sModule, 'block');
    }

    public function getModuleComponent($sModule, $sComponent)
    {
        return Linko::Database()->table('module_component')
            ->select()
            ->where('module_id', '=', $sModule)
            ->where('component_type', '=', $sComponent)
            ->query()
            ->fetchRows();
    }

    public function cleanup()
    {
        $aModules = array_keys($this->getModules());

        $aRows = Linko::Database()->table('module')
            ->select('module_id')
            ->query()
            ->fetchRows();

        foreach($aRows as $aRow)
        {
            if(!in_array($aRow['module_id'], $aModules))
            {
                Linko::Model('Module/Action')->uninstall($aRow['module_id']);
            }
        }
    }
}

?>