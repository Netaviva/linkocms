<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage page : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Page_Plugin_Admincp
{
	public function init()
	{
		// Add Admincp Routes
		Linko::Model('Admincp')->addRoute('page', array(
			'id' => 'page:admincp',
			'controller' => 'page/admincp/index'
		));

        Linko::Model('Admincp')->addRoute('page/layout(/[page_id](/[action](/[id])))', array(
            'id' => 'page:admincp:layout',
            'controller' => 'page/admincp/layout/index',
            'rules' => array(
				'module' => '[a-zA-Z0-9_-\.:]',
				'action' => 'assign-block|edit-block|delete-block',
				'id' => ':int',
			)
        ));

		Linko::Model('Admincp')->addRoute('page/[action](/[id])', array(
			'id' => 'page:admincp:action',
			'controller' => 'page/admincp/action',
			'rules' => array(
				'id' => ':int',
				'action' => 'add|edit|delete'
			)
		));	
        		
		// Add Menus
		Linko::Model('Admincp')->addMenu('Pages', array(
			'Add Page' => Linko::Url()->make('page:admincp:action', array('action' => 'add')),
			'Manage Pages' => Linko::Url()->make('page:admincp')
		));	
	}
}

?>