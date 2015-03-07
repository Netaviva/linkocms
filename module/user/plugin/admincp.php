<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class User_Plugin_Admincp
{
	public function init()
	{
		Linko::Model('Admincp')->addRoute('user(/[page])', array(
			'id' => 'user:admincp',
			'controller' => 'user/admincp/index',
			'rules' => array(
				'page' => ':int'
			)
		));

		Linko::Model('Admincp')->addRoute('user/[action](/[id])', array(
			'id' => 'user:admincp:action',
			'controller' => 'user/admincp/action',
			'rules' => array(
				'id' => ':int',
				'action' => 'add|edit|delete'
			)
		));
		
		Linko::Model('Admincp')->addRoute('user/role', array(
			'id' => 'user:admincp:role',
			'controller' => 'user/admincp/role/index'
		));

        Linko::Model('Admincp')->addRoute('user/role/[action](/[id])', array(
            'id' => 'user:admincp:role:action',
            'controller' => 'user/admincp/role/action',
            'rules' => array(
                'id' => ':int',
                'action' => 'add|edit|delete'
            )
        ));

		Linko::Model('Admincp')->addMenu('Users', array(
			'Browse Users' => Linko::Url()->make('user:admincp'),
			'User Roles' => Linko::Url()->make('user:admincp:role')
		));
	}
}

?>