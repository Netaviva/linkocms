<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage block : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Block_Plugin_Admincp
{
	public function init()
	{
		// Add Admincp Routes
        Linko::Model('Admincp')->addRoute('block(/page-[page_id])', array(
            'id' => 'block:admincp',
            'controller' => 'block/admincp/index',
            'rules' => array(

            )
        ));
        		
		// Add Menus
        // Instead of creating a new menu, lets just hook it to the Pages Menu
		Linko::Model('Admincp')->addMenu('Pages', array(
            'Block Manager' => Linko::Url()->make('block:admincp')
		));	
	}
}

?>