<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage admincp : model - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Admincp_Model_Admincp extends Linko_Model
{
	private $_aRoutes = array();
	
	private $_aMenus = array();
	
	private $_aDashboard = array();
    
    private $_aMenuCategory = array();
        
	public function __construct()
	{

	}
	
	public function init()
	{
		// build default routes
		$this->_aRoutes = array(
			'system/phpconf' => array('controller' => 'admincp/system/phpconf'),
			'system/server' => array('controller' => 'admincp/system/server'),
		);
		
		// Build default Menus
		$this->_aMenus['Dashboard'] = $this->url('');
	
		$this->_aMenus['Settings'] = array();
        $this->_aMenus['Module'] = array();
        $this->_aMenus['Theme'] = array();
        $this->_aMenus['Localization'] = array();
        $this->_aMenus['Pages'] = array();
        $this->_aMenus['Menus'] = array();

		$this->_aMenus['System'] = array(
			'Php Configuration' => $this->url('system/phpconf'),
			'Server Environment' => $this->url('system/server')
		);
        
        Linko::Plugin()->call('admincp.init');
        
        $this->addMenuCategory('main', array(
            'Dashboard'
        ));
        
        $this->addMenuCategory('required_modules', array(
            'Settings', 
            'Module',
            'Theme', 
            'Localization', 
            'Menus', 
            'Pages'
        ));
	}
	
	public function setController($sUri)
	{
        // add routes
		foreach($this->_aRoutes as $sRoute => $aRoute)
		{
			Linko::Router()->add($sRoute, $aRoute);
		}
        
        // reroute the request
		$oRoute = Linko::Router()->route($sUri);
		
		if($sUri)
		{
			if($oRoute->controller == 'error/404')
			{
				Linko::Module()->set('admincp/error/404');		
			}
			else
			{
				// sets the controller based on the new routes
				Linko::Module()->set($oRoute->controller, $oRoute->args);
			}
		}
		else
		{
			Linko::Module()->set('admincp/dashboard');
		}
	}

	public function getMenu($mCategory = null)
	{
        if($mCategory)
        {
            $aMenus = array();
            
            if(!is_array($mCategory))
            {
                $mCategory = array($mCategory);
            }
            
            foreach($mCategory as $sCategory)
            {
                if(array_key_exists($sCategory, $this->_aMenuCategory))
                {
                    $aMenus += $this->_aMenuCategory[$sCategory];
                }                
            }
            
            return $aMenus;
        }

		return $this->_aMenus;	
	}

    public function getSelectedMenu()
    {
        $mGroup = null;

        foreach($this->_aMenus as $sGroup => $mMenus)
        {
            if(is_array($mMenus))
            {
                foreach($mMenus as $mMenu)
                {
                    if($mMenu == Linko::Url()->make('self'))
                    {
                        $mGroup = $sGroup;

                        break;
                    }
                }
            }
            else
            {
                if(rtrim($mMenus, '/') == Linko::Url()->make('self'))
                {
                    $mGroup = $sGroup;

                    break;
                }
            }
        }

        return $mGroup;
    }
    	
	public function addMenu($sTitle, $mMenu)
	{
        if(isset($this->_aMenus[$sTitle]))
        {
            if(is_array($mMenu))
            {
                $this->_aMenus[$sTitle] = array_merge($this->_aMenus[$sTitle], $mMenu);
            }
            else
            {
                $this->_aMenus[$sTitle] = $mMenu;
            }
            
            return $this;
        }
        
		$this->_aMenus[$sTitle] = $mMenu;

        $this->addMenuCategory('other_modules', array($sTitle));

        return $this;
	}
    
    public function addMenuCategory($mCategory, $aMenus)
    {
        if(!is_array($mCategory))
        {
            $mCategory = array($mCategory);
        }
        
        foreach($mCategory as $sCategory)
        {
            if(!array_key_exists($sCategory, $this->_aMenuCategory))
            {
                $this->_aMenuCategory[$sCategory] = array();
            }
            
            foreach($aMenus as $sMenu)
            {
                $this->_aMenuCategory[$sCategory][$sMenu] = $this->_aMenus[$sMenu];   
            }
        }
        
        return $this;
    }
    
	public function addDashboard($sPosition, $aParam)
	{
        $this->_aDashboard[$sPosition][] = $aParam;
        
        return $this;
	}
    
    public function getDashboard($sPosition)
    {
        $sHtml = null;
        
        if(!isset($this->_aDashboard[$sPosition]))
        {
            return $sHtml;
        }
        
        foreach($this->_aDashboard[$sPosition] as $aDashboard)
        {
            extract($aDashboard);
            
            if(isset($type) && $type == 'block')
            {
                $content = Linko::Module()->getBlock($content);
            }
            
            $sHtml .= $content;
        }
        
        return $sHtml;
    }
    	
	public function addRoute($sRoute, $aParams = array())
	{
		Linko::Router()->add('module/' . $sRoute, $aParams);
		
		return $this;
	}

	public function urlModule($sUrl)
	{
		return Linko::Url()->make('admincp', array('uri' => 'module/' . $sUrl));	
	}
		
	public function url($sUrl)
	{
		return Linko::Url()->make('admincp', array('uri' => $sUrl));	
	}
}

?>