<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage extension : model - module\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Module_Model_Action extends Linko_Model
{
	public function install($sModule)
	{
		// Check if the module is already installed
		$bExists = Linko::Model('Module')->isInstalled($sModule);
			
		if($bExists)
		{
            // Perhaps the install link still shows as a result of cached data
            $this->clearCache();

            //exit;
			return array(false, 'Module Already Exists.');
		}
		
		// Read the module manifest
		if(!$aManifest = Linko::Model('Module')->getManifest($sModule))
		{

			return array(false, 'Manifest File Not Found.');	
		}

		// Start: Install Db Tables
		if(isset($aManifest['table']) && ($aManifest['table'] != ''))
		{
			$aTables = unserialize(trim($aManifest['table']));

			foreach($aTables as $sTable => $aFields)
			{
				Linko::Database()->table($sTable)
					->create($aFields, true)
					->query();
			}
		}
		// End: Install Db Tables

        // Start Install Component:Controllers
        if(isset($aManifest['component']['controller']))
        {
            $aControllers = isset($aManifest['component']['controller'][1]) ? $aManifest['component']['controller'] : array($aManifest['component']['controller']);
            
            foreach($aControllers as $aController)
            {
                if(!is_array($aController))
                {
                    $aController = array($aController);
                }
                
				// make sure required attributes of the controller are set
				if(!Arr::hasKeys($aController, 'path', 'route_id', 'route_url', 'label'))
				{
					continue;	
				}

                $aController = array_merge(array(
                    'title' => NULL,
                    'is_page' => false,
                    'hidden' => false,
                    'route_rule' => array(),
                ), $aController);

                $iComponentId = $this->addComponent(array(
                    'module_id' => $sModule,
                    'component_type' => 'controller',
                    'route_id' => $aController['route_id'],
                    'route_rule' => $aController['route_rule'],
                    'path' => $aController['path'],
                    'label' => $aController['label']
                ));

                // insert into page
                if($iComponentId)
                {
                    // @todo use page model
                    Linko::Database()->table('page')->insert(array(
                        'page_url' => $aController['route_url'],
                        'page_title' => $aController['label'],
                        'page_type' => 'module',
                        'page_content' => null,
                        'component_id' => $iComponentId,
                        'page_status' => 1, // set to active
                        'page_layout' => null,
                        'dissallow_access' => serialize(array()),
                        'meta_title' => $aController['title'],
                        'time_created' => Date::now(),
                        'time_updated' => Date::now(),
                        'is_hidden' => (boolean)$aController['hidden']
                    ))->query()->getInsertId();
                }            
            }
        }
        // End Install Component:Controllers
        
        // Start Install Component:Block
        if(isset($aManifest['component']['block']))
        {
            foreach($aManifest['component']['block'] as $aBlock)
            {
                if(!is_array($aBlock))
                {
                    $aBlock = array($aBlock);
                }
                
				// make sure required attributes of the block are set
				if(!Arr::hasKeys($aBlock, array('path', 'label')))
				{
					continue;	
				}
                
                // insert into module_component
                Linko::Database()->table('module_component')->insert(array(
                    'module_id' => $sModule,
                    'route_id' => null,
                    'component_type' => 'block',
                    'component_file' => $aBlock['path'],
                    'component_label' => $aBlock['label']
                ))->query()->getInsertId();
             }
        }
        // End Install Component:Block
		
		// Some cleanup
		// Delete the settings of this module if it exists due to bad uninstallation or something similar
		Linko::Database()->table('setting')
			->delete()
			->where("module_id = :module_id")
			->query(array(':module_id' => $sModule));
		
		// Start Add Settings
		if(isset($aManifest['settings']['setting']))
		{
			$aSettings = isset($aManifest['settings']['setting'][1]) ? $aManifest['settings']['setting'] : array($aManifest['settings']['setting']);
			
			foreach($aSettings as $aSetting)
			{
                if(!is_array($aSetting))
                {
                    $aSetting = array($aSetting);
                }
                
				// skip settings that didnt include required params
				if(!Arr::hasKeys($aSetting, array('var', 'type', 'value')))
				{
					continue;	
				}

                $aSetting = array(
                    'module_id' => $sModule,
                    'var' => $aSetting['var'],
                    'type' => $aSetting['type'],
                    'value' => $aSetting['value'],
                    'title' => isset($aSetting['title']) ? $aSetting['title'] : ucwords(str_replace('_', ' ', $aSetting['var'])),
                    'description' => isset($aSetting['description']) ? $aSetting['description'] : 'NA',
                    'data' => isset($aSetting['data']) ? $aSetting['data'] : ''
		        );

                Linko::Model('Setting/Action')->add($sModule, $aSetting);
			}			
		}		
		// End: Add Settings

		// Start Add Translations
		if(isset($aManifest['translations']['translation']))
		{
            $aTranslations = isset($aManifest['translations']['translation'][1]) ? $aManifest['translations']['translation'] : array($aManifest['translations']['translation']);
			
			foreach($aTranslations as $aTranslation)
			{
                if(!is_array($aTranslation))
                {
                    $aTranslation = array($aTranslation);
                }
                
				// skip translations that didnt include required params
				if(!Arr::hasKeys($aTranslation, array('var')))
				{
					continue;	
				}

				Linko::Model('Locale/Language/Action')->addTranslation($aTranslation['var'], $aTranslation['value'], $sModule, 'en_GB');
			}			
		}		
		// End: Add Translations

        // Add Module
		Linko::Database()->table('module')
			->delete()
			->where('module_id', '=', $sModule)
			->query();

        $bAutoEnable = (bool)isset($aManifest['auto_enable']) ? $aManifest['auto_enable'] : false;

		Linko::Database()->table('module')
			->insert(array(
				'module_id' => $sModule,
				'version' => $aManifest['version']
			))->query();

        if($bAutoEnable)
        {
            $this->enable($sModule, false);
        }

		// Call module task module_install()
		if(Linko::Module()->hasTask($sModule, 'module_install'))
		{
			Linko::Module()->callTask($sModule, 'module_install');
		}

        $this->clearCache();
        
		return array(true, $sModule . ' Module Installed');	
	}
	
	public function uninstall($sModule)
	{
		// Check if the module is already installed
		if(false === Linko::Model('Module')->isInstalled($sModule))
		{
            $this->clearCache();
            
			//return array(false, 'Module Does Not Exists.');
		}

		// Read the module manifest
		if(!$aManifest = Linko::Model('Module')->getManifest($sModule))
		{
			//return array(false, 'Manifest File Not Found.');
		}
				
		// Remove Pages
		Linko::Database()->table('page')
            ->delete()
            ->whereIn('component_id', (
                Linko::Database()->table('module_component')
                ->select('component_id')
                ->where('module_id', '=', $sModule)
            ))
            ->query();
		
        // Remove Page Blocks
        Linko::Model('Block/Action')->deleteModuleBlocks($sModule);
            
        // Remove Components
        Linko::Database()->table('module_component')
            ->delete()
            ->where('module_id', '=', $sModule)
            ->query();
        
		// Remove Setting
		Linko::Database()->table('setting')
            ->delete()
            ->where('module_id', '=', $sModule)
            ->query();
		
        if(Linko::Module()->isModule('locale'))
        {
            // Remove Translations
            Linko::Model('Locale/Language/Action')->deleteModuleTranslations($sModule);
        }
        
		// Remove Module
		Linko::Database()->table('module')
			->delete()
			->where('module_id', '=', $sModule)
			->query();

		// Remove Tables
		if(isset($aManifest['table']))
		{
			$aTables = array_keys(unserialize(trim($aManifest['table'])));
			
            foreach($aTables as $sTable)
            {
                Linko::Database()->table($sTable)->drop()->query();   
            }
		}

		// Call module task module_uninstall()
		if(Linko::Module()->hasTask($sModule, 'module_uninstall'))
		{
			Linko::Module()->callTask($sModule, 'module_uninstall');
		}

        $this->clearCache();
        
        return array(true, $sModule . ' Module Uninstalled');	
	}

	/**
	 * Performs an upgrade of a module
	 *
	 * @param string $sModule module id
	 * @param string $sVersion the previous version
     * @return array
     */
	public function upgrade($sModule, $sVersion)
	{
        // Read the module manifest
        if(!$aManifest = Linko::Model('Module')->getManifest($sModule))
        {
            return array(false, 'Manifest File Not Found.');
        }

		// create tables that do not exists in database
        if(isset($aManifest['table']) && ($aManifest['table'] != ''))
        {
            $aTables = unserialize(trim($aManifest['table']));

            foreach($aTables as $sTable => $aFields)
            {
                if(!Linko::Database()->table($sTable)->exists())
                {
                    Linko::Database()->table($sTable)
                        ->create($aFields, true)
                        ->query();
                }
            }
        }

        // auto manage settings
        if(isset($aManifest['settings']['setting']))
        {
            $aSettings = Linko::Model('Setting')->getSettings($sModule, true);
            $aInstalled = array();

            foreach($aSettings as $aSetting)
            {
                $aInstalled[$aSetting['setting_var']] = false;
            }

            $aSettings = array();

            // Check for settings in the manifest that does not exists and add them.
            foreach($aManifest['settings']['setting'] as $aSetting)
            {
                var_dump($aSetting);
                if(!isset($aInstalled[$aSetting['var']]))
                {
                    $aVals = array();

                    $aVals = array_merge(array(
                        'value' => '',
                        'title' => '',
                        'type' => '',
                        'value' => '',
                        'title' => '',
                        'description' => '',
                        'data' => array()
                    ), $aSetting);

                    Linko::Model('Setting/Action')->add($sModule, $aVals);
                }

                $aInstalled[$aSetting['var']] = true;
            }

            // While those that are not in the manifest, remove them.
            foreach($aInstalled as $sVar => $bFound)
            {
                if(!$bFound)
                {
                    Linko::Model('Setting/Action')->deleteSetting($sModule, $sVar);
                }
            }

        }

        // auto manage translations
        if(isset($aManifest['translations']['translation']))
        {
            $aTranslations = Linko::Database()->table('language_translation')
                ->select()
                ->where('locale_id', '=', 'en_GB')
                ->where('module_id', '=', $sModule)
                ->query()
                ->fetchRows();

            $aInstalled = array();

            foreach($aTranslations as $aTranslation)
            {
                $aInstalled[$aTranslation['translation_var']] = false;
            }

            $aTranslations = array();

            // Check for settings in the manifest that does not exists and add them.
            foreach($aManifest['translations']['translation'] as $aTranslation)
            {
                if(!isset($aInstalled[$aTranslation['var']]))
                {
                    Linko::Model('Locale/Language/Action')->addTranslation($aTranslation['var'], $aTranslation['value'], $sModule);
                }

                $aInstalled[$aTranslation['var']] = true;
            }

            foreach($aInstalled as $sVar => $bFound)
            {
                if(!$bFound)
                {
                    Linko::Model('Locale/Language/Action')->deleteTranslation($sVar);
                }
            }
        }
        // end: auto manage translations

        // start: auto manage controller
        if(isset($aManifest['component']['controller']))
        {
            $aControllers = Linko::Model('Module')->getModuleControllers($sModule);
            $aInstalled = array();

            foreach($aControllers as $aController)
            {
                $aInstalled[$aController['component_file']] = false;
            }

            $aControllers = array();

            // Check for settings in the manifest that does not exists and add them.
            foreach($aManifest['component']['controller'] as $aController)
            {
                if(!isset($aInstalled[$aController['path']]))
                {
                    $iComponentId = $this->addComponent(array_merge(array(
                        'module_id' => $sModule,
                        'component_type' => 'controller'
                    ), $aController));

                    $aController['title'] = isset($aController['title']) ? $aController['title'] : $aController['label'];

                    Linko::Model('Page/Action')->add(array(
                        'page_title' => $aController['title'],
                        'page_status' => 1,
                        'component_id' => $iComponentId,
                        'meta_title' => $aController['title'],
                        'page_url' => $aController['route_url'],
                        'page_type' => 'module'
                    ));
                }

                $aInstalled[$aController['path']] = true;
            }

            foreach($aInstalled as $sVar => $bFound)
            {
                // remove controller
            }
        }
        // end: auto manage controllers

        // start: auto manage block

        // end: auto manage block

        // Do pretty much anything else you want to do in task module_upgrade().
        // the version currently in use is returned as the first argument
		if(Linko::Module()->hasTask($sModule, 'module_upgrade'))
		{
			Linko::Module()->callTask($sModule, 'module_upgrade', $sVersion);
		}

        $this->clearCache();

        return array(true, 'Module Upgraded');
	}
	
	public function enable($sModule, $bCheck = true)
	{
        if($bCheck)
        {
            if(false === Linko::Model('Module')->isInstalled($sModule))
            {
                $this->clearCache();

                return array(false, 'Module Not Installed.');
            }

            if(Linko::Model('Module')->isEnabled($sModule))
            {
                $this->clearCache();

                return array(false, 'Module Already Enabled.');
            }
        }
		
		Linko::Database()->table('module')
			->update(array('enabled' => true))
			->where('module_id', '=', $sModule)
			->query();

        // Call module task module_enable()
        if(Linko::Module()->hasTask($sModule, 'module_enable'))
        {
            Linko::Module()->callTask($sModule, 'module_enable');
        }

        $this->clearCache();
        
		return array(true, $sModule . ' Module Enabled');	
	}
	
	public function disable($sModule)
	{
		$bExists = Linko::Module()->isModule($sModule);
		
		// Cannot disable a module that does not exists	
		if(!$bExists)
		{
            $this->clearCache();
            
			return array(false, 'Module Does Not Exists.');
		}
		
		Linko::Database()->table('module')
			->update(array('enabled' => false))
			->where('module_id', '=', $sModule)
			->query();

        // Call module task module_disable()
        if(Linko::Module()->hasTask($sModule, 'module_disable'))
        {
            Linko::Module()->callTask($sModule, 'module_disable');
        }
		
        $this->clearCache();
        
		return array(true, $sModule . ' Module Disabled');	
	}

    public function addComponent($aVals)
    {
        if(!Arr::hasKeys($aVals, 'module_id', 'component_type', 'path', 'route_id', 'label'))
        {
            return false;
        }

        $aVals = array_merge(array(
            'title' => NULL,
            'is_page' => false,
            'hidden' => false,
            'route_rule' => array(),
        ), $aVals);

        if(is_string($aVals['route_rule']))
        {
            parse_str($aVals['route_rule'], $aVals['route_rule']);
        }

        $iId = Linko::Database()->table('module_component')
            ->insert(array(
                'module_id' => $aVals['module_id'],
                'route_id' => $aVals['route_id'],
                'route_rule' => serialize($aVals['route_rule']),
                'component_type' => $aVals['component_type'],
                'component_file' => $aVals['path'],
                'component_label' => $aVals['label']
            ))
            ->query()
            ->getInsertId();

        return $iId;
    }
    
    public function clearCache()
    {
        Linko::Cache()->delete(array('extension', 'modules'));
        Linko::Cache()->delete(array('extension', 'module_settings'));
        Linko::Cache()->delete(array('application', 'plugin'));
        Linko::Cache()->delete(array('application', 'module_loaders'));
    }
}

?>