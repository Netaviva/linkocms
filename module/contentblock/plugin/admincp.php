<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage contentblock : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Contentblock_Plugin_Admincp
{
	public function init()
	{
		Linko::Model('Admincp')->addRoute('contentblock', array(
			'id' => 'contentblock:admincp',
			'controller' => 'contentblock/admincp/index'
		));

		Linko::Model('Admincp')->addRoute('contentblock/[action](/[id])', array(
			'id' => 'contentblock:admincp:action',
			'controller' => 'contentblock/admincp/action',
			'rules' => array('action' => 'add|edit|delete', 'id' => ':int')
		));
		
		Linko::Model('Admincp')->addMenu('Content Blocks', Linko::Url()->make('contentblock:admincp'));
	}
}

?>