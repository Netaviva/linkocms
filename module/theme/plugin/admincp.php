<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage theme : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Theme_Plugin_Admincp
{
    public function init()
    {
        Linko::Model('Admincp')->addRoute('theme', array(
			'id' => 'theme:admincp', 
			'controller' => 'theme/admincp/index',
        ));

        Linko::Model('Admincp')->addRoute('theme/[theme]-[type]', array(
            'id' => 'theme:admincp:view',
            'controller' => 'theme/admincp/view',
            'rules' => array(
                'theme' => ':alnum',
                'type' => ':alnum'
        )));

        Linko::Model('Admincp')->addRoute('theme', array(
            'id' => 'admincp:theme',
            'controller' => 'admincp/theme/index'
        ));
        
        Linko::Model('Admincp')->addMenu('Theme', Linko::Url()->make('theme:admincp'));
    }
}
?>
