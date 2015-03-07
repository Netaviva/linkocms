<?php

/**
 * Core File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

defined('LINKO') or exit();

class CMS
{
	const USER_ROLE_ADMIN = 1;
    
    const USER_ROLE_USER = 2;
    
    const USER_ROLE_GUEST = 3;
    
	private $_linko;
	
	public function __construct()
	{
	   $this->_linko = new Linko;

        $this->_linko->startApplication();

		require INCLUDE_DIR . 'constant.php';

		require INCLUDE_DIR . 'extend.php';

        $this->_registerTemplatePlugins();

        if(Linko::Config()->get('application.installed') == false && Linko::Url()->segment(1) != 'install' && !Linko::Request()->isAjax())
        {
            exit('<html><head></head><body style="background-color: #0078a3; color: #FFFFFF;">No need to worry. This is not the same as "blue screen of death" yet!. You just need to run install and your site will be up and ready!</body></html>');
        }

        if(Linko::Config()->get('application.installed'))
        {
            Linko::Model('Module')->init();

            Linko::Model('Setting')->init();

            Linko::Model('Locale')->init();

            Linko::Model('Theme')->init();

            Linko::Model('Page')->init();
        }

		require INCLUDE_DIR . 'config/session.php';
	}
	
    public function runAjaxApplication()
    {
        $this->_linko->runApplication('ajax');        
    }
    
	public function runWebApplication()
	{
        // Set the error 404 page
		Linko::Module()->setAlias('_404_', 'page/error/404');

		Linko::Module()->add('install', array('enabled' => true));

		if(Linko::Config()->get('application.installed'))
		{
			// Set Script
			Linko::Template()->setScript(array(
				'jquery/jquery.js',
                'jquery/jquery.simplemodal.js',
                'jquery/jquery.form.js',
                'underscore.js',
	            'linko.js'
			), 'asset_js')
	        ->setHeader(Html::script('var Config = ({
	            base_url: \'' . Linko::Url()->path() . '\',
	            cookie_prefix: \'' . Linko::Config()->get('cookie.prefix') . '\',
	            cookie_path: \'' . Linko::Config()->get('cookie.path') . '\',
	            cookie_domain: \'' . Linko::Config()->get('cookie.domain') . '\',
	            asset_image: \'' . Linko::Url()->path('asset/image') . '\',
	            ajax_url: \'' . Linko::Url()->path('asset') . 'ajax.php\',
	            current_url: \'' . Linko::Url()->getFull() . '\'
	        });'))
            ->setStyle('linko.css', 'asset_css')
	        ->setStyle(array(
				'style.css'
			), 'theme_css');
		}

        // Process Application
        $this->_linko->runApplication();
        
		// Clears Flash Message
		Linko::Flash()->clear();
	}

	public function parse()
	{

	}
    
    private function _registerTemplatePlugins()
    {
        // Register Template Plugins
        Linko::Template()->registerPlugin('pager', INCLUDE_DIR . 'template' . DS . 'pager.php');
    }
}