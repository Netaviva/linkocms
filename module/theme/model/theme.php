<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage theme : model - theme.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Theme_Model_Theme extends Linko_Model
{
    private $_aThemes = array();
    
    private $_aTypes = array();

	/**
	 * Sets the theme
	 */    	
    public function init()
    {
        $aThemes = $this->getThemes();

        $aTypes = array_keys($aThemes);

        foreach($aTypes as $sType)
        {
            $aTheme = array();
            
            foreach($aThemes[$sType] as $sTheme => $mTheme)
            {
                $aTheme[$sTheme] = $mTheme['default'] == 1 ? true : false;

	            Linko::Template()->addTheme($sType, $aTheme);

	            $aSetting = array();

	            if(isset($mTheme['settings']['setting']))
	            {
		            foreach($mTheme['settings']['setting'] as $mSetting)
		            {
			            if(!isset($mTheme['setting'][$mSetting['var']]))
			            {
				            $aSetting[$mSetting['var']] = $mSetting['value'];
			            }
			            else
			            {
				            $aSetting[$mSetting['var']] = $mTheme['setting'][$mSetting['var']];
			            }
		            }

		            Linko::Template()->addSetting($sType, $sTheme, $aSetting);
	            }
            }
        }
    }
       
	public function setDefault($sTheme, $sType)
	{
		$bExists = $this->isInstalled($sTheme, $sType);
		
		// reset the default theme
		Linko::Database()->table('theme')
			->update(array('theme_default' => 0))
			->where('theme_default', '=', 1)
            ->where('theme_type', '=', $sType)
			->query();
		
		if(!$bExists)
		{
			// Install the theme and set it as default
			Linko::Database()->table('theme')
				->insert(array(
					'theme_folder' => $sTheme,
					'theme_type' => $sType,
					'theme_default' => 1
				)
			)->query();
		}
		else
		{
			// set theme as default
			Linko::Database()->table('theme')
				->update(array('theme_default' => 1))
				->where('theme_folder', '=', $sTheme)
                ->where('theme_type', '=', $sType)
				->query();
		}
		
		return true;
	}

	/**
	 * Get the currently activated theme for the type
	 */
	public function getDefault($sType = 'frontend')
	{
        Linko::Cache()->set(array('theme', 'default_' . $sType));
        
        if(!$sTheme = Linko::Cache()->read())
        {
    		$sTheme = Linko::Database()->table('theme')
    			->select('theme_folder')
    			->where('theme_default', '=', 1)
                ->where('theme_type', '=', $sType)
    			->query()
                ->fetchValue();
            
            Linko::Cache()->write($sTheme);       
        }
        
        return $sTheme;		
	}
    
	public function getThemes()
	{
        $sId = Linko::Cache()->set(array('theme', 'themes'));
        
        if(!$aThemes = Linko::Cache()->read($sId))
        {
    		// Get the installed themes
            $aInstalled = $this->getInstalledThemes();
            
    		$aThemes = array();

    		$aFolder = Dir::getFolders(Linko::Config()->get('dir.theme'));
    		
    		// Build Theme from folder
    		foreach($aFolder as $sFolder)
    		{
    			$aSupportType = Dir::getFolders($sFolder);
    			
    			foreach($aSupportType as $sSupportType)
    			{
    				$sType = pathinfo($sSupportType, PATHINFO_BASENAME);
    				
    				if(!array_key_exists($sType, $aThemes))
    				{
    					$aThemes[$sType] = array();
    				}
    				
    				$sThemeId = basename($sFolder);

                    if(!$aManifest = $this->getManifest($sThemeId, $sType))
                    {
                        continue;
                    }

    				$aThemes[$sType][$sThemeId] = array_merge($aManifest, array(
    					'screenshot' => Linko::Url()->path($sSupportType) . 'screenshot.png',
    					'path' => $sFolder
    				));

                    $sSetting = isset($aInstalled[$sType][$sThemeId]['theme_setting']) ? $aInstalled[$sType][$sThemeId]['theme_setting'] : array();

    				$aThemes[$sType][$sThemeId]['default'] = (isset($aInstalled[$sType][$sThemeId]) && $aInstalled[$sType][$sThemeId]['theme_default'] == 1) ? true : false;

    				$aThemes[$sType][$sThemeId]['installed'] = isset($aInstalled[$sType][$sThemeId]) ? true : false;

                    $aThemes[$sType][$sThemeId]['setting'] = $sSetting ? unserialize($sSetting) : array();
                }
                
                Linko::Cache()->write($aThemes, $sId);
    		}
        }

		return $aThemes;
	}
	
    public function getTheme($sType, $sTheme)
    {
        $aThemes = $this->getThemes();
        
        if(array_key_exists($sType, $aThemes) && (array_key_exists($sTheme, $aThemes[$sType])))
        {
            return $aThemes[$sType][$sTheme];
        }
        
        return false;
    }

	public function getThemeSettings($sType, $sTheme)
	{
		if(!$aTheme = $this->getTheme($sType, $sTheme))
		{
			return array();
		}

		$aSettings = array();

		foreach($aTheme['settings']['setting'] as $aSetting)
		{
			if(!isset($aSetting['var']))
			{
				continue;
			}

			$aSettings[] = array_merge(array(
				'label' => isset($aSetting['label']) ?: Inflector::humanize($aSetting['var']),
				'type' => 'text'
			), $aSetting);
		}

		return $aSettings;
	}
    
    public function getDefaultThemes()
    {
        $aThemes = Linko::Template()->getThemes();
        
        $aDefault = array();
        
        foreach(array_keys($aThemes) as $sType)
        {
            foreach($aThemes[$sType] as $sTheme => $bDefault)
            {
                if($bDefault)
                {
                    $aDefault[$sType] = $this->getTheme($sType, $sTheme);
                }
            }
        }
        
        return $aDefault;
    }
    
    public function getTypes()
    {
        return ;
    }
    
	public function getManifest($sTheme, $sType)
	{
		$sManifest = Linko::Config()->get('dir.theme') . $sTheme . DS . $sType . DS . 'manifest.xml';
		
		if(!File::exists($sManifest))
		{
			return false;	
		}
		
		$aManifest = array_merge(array(
            'author' => 'NA',
			'version' => '1.0.0',
			'description' => 'NA',
            'layout_definition' => array(),
			'layouts' => array(),
            'settings' => array(),
            'menu' => array('location' => array())
		), Linko::Xml()->parse($sManifest));

		if(!isset($aManifest['layouts']['layout']))
		{
			$aManifest['layouts']['layout'] = array();	
		}
		
		if(count($aManifest['layouts']['layout']) && !isset($aManifest['layouts']['layout'][0]))
		{
			$aManifest['layouts']['layout'] = array($aManifest['layouts']['layout']);	
		}

        if(!isset($aManifest['settings']['setting']))
        {
            $aManifest['settings']['setting'] = array();
        }

        if(count($aManifest['settings']['setting']) && !isset($aManifest['settings']['setting'][0]))
        {
            $aManifest['settings']['setting'] = array($aManifest['settings']['setting']);
        }

        if(count($aManifest['menu']['location']) && !isset($aManifest['menu']['location'][0]))
        {
            $aManifest['menu']['location'] = array($aManifest['menu']['location']);
        }

		return $aManifest;
	}
	
    public function getInstalledThemes()
    {
        $sCacheId = Linko::Cache()->set(array('theme', 'installed_themes'));
        
        if(!$aInstalled = Linko::Cache()->read($sCacheId))
        {
    		$aRows = Linko::Database()->table('theme')
                ->select('theme_default, theme_type, theme_folder, theme_setting')
                ->query()
                ->fetchRows();
      
            foreach($aRows as $aRow)
            {
                $aInstalled[$aRow['theme_type']][$aRow['theme_folder']] = $aRow;
            }

            Linko::Cache()->write($aInstalled, $sCacheId);           
        } 
        
        return $aInstalled;       
    }
    
    public function isInstalled($sTheme, $sType)
    {
        $aThemes = $this->getInstalledThemes();
        
        if((isset($aThemes[$sType])) && (isset($aThemes[$sType][$sTheme])))
        {
            return true;
        }
        
        return false;
    }
    
	public function isLayout($sLayout, $sType = 'frontend')
	{
		return File::exists(Linko::Config()->get('dir.theme') . $this->getDefault($sType) . DS . $sType . DS . 'layout' . DS . $sLayout . Linko::Config()->get('ext.theme'));
	}
	
	public function getLayouts($sType = 'frontend')
	{
		$aManifest = $this->getManifest($this->getDefault($sType), $sType);

		return (array)$aManifest['layouts']['layout'];
	}
	
	public function getPositions($sLayout = null, $sType = 'frontend')
	{
		$aLayouts = $this->getLayouts($sType);
		$aPositions = array();

        if(!count($aLayouts))
        {
            return array();
        }

		foreach($aLayouts as $aLayout)
		{
			if(isset($aLayout['position']))
			{
				// Make sure the array is 2-Dim
				if(count($aLayout['position']) && !isset($aLayout['position'][0]))
				{
					$aLayout['position'] = array($aLayout['position']);
				}
					
				if($sLayout)
				{
					if($aLayout['name'] == $sLayout)
					{				
						$aPositions[$aLayout['title']] = $aLayout['position'];
					}
					
					// Also include partial layouts.
					if(isset($aLayout['type']) && $aLayout['type'] == 'partial')
					{
						$aPositions[$aLayout['title']] = $aLayout['position'];
					}
				}
				else
				{
					$aPositions[$aLayout['title']] = $aLayout['position']; 
				}
			}
		}

		return $aPositions;
	}
}

?>