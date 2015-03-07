<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage menu : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Menu_Plugin_Admincp
{
	public function init()
	{
		Linko::Model('Admincp')->addRoute('menu(/menu-[menu_id])', array(
			'id' => 'menu:admincp',
			'controller' => 'menu/admincp/index',
            'rules' => array('menu_id' => ':int')
		));

		Linko::Model('Admincp')->addRoute('menu(/[action](/[id]))', array(
			'id' => 'menu:admincp:action',
			'controller' => 'menu/admincp/action',
			'rules' => array('id' => ':int', 'action' => 'add|edit|delete')
		));

        Linko::Model('Admincp')->addRoute('menu/menu-[menu_id]/item(/[action](/[item_id]))', array(
            'id' => 'menu:admincp:item:action',
            'controller' => 'menu/admincp/action-item',
            'rules' => array(
                'menu_id' => ':int',
                'item_id' => ':int',
                'action' => 'add|edit|delete'
            )
        ));

		Linko::Model('Admincp')->addMenu('Menus', array(
			'Add Menu' => Linko::Url()->make('menu:admincp:action', array('action' => 'add')),
			'Manage Menus' => Linko::Url()->make('menu:admincp')
		));				
	}
}

?>