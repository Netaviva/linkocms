<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage module : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Module_Plugin_Admincp
{
    public function init()
    {
        Linko::Model('Admincp')->addRoute('module(/[action]-[module])', array(
			'id' => 'module:admincp',
			'controller' => 'module/index',
			'rules' => array(
				'action' => 'disable|enable|install|uninstall'
        )));

	    Linko::Model('Admincp')->addRoute('module/install(/[goto])', array(
		    'id' => 'module:install',
		    'controller' => 'module/install',
		    'rules' => array(
				'goto' => 'upload|remote'
			)
	    ));

        Linko::Model('Admincp')->addMenu('Module', Linko::Url()->make('module:admincp'));
    }
}
?>
