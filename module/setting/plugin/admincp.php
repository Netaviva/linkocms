<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage setting : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Setting_Plugin_Admincp
{
    public function init()
    {
		Linko::Model('Admincp')->addRoute('setting(/[category](/[item]))', array(
			'id' => 'setting:admincp',
			'controller' => 'setting/admincp/index',
            'rules' => array(
                
            )
		));
        
        Linko::Model('Admincp')->addMenu('Settings', array(
	        'Manage Settings' => Linko::Url()->make('setting:admincp')
	    ));
    }
}

?>