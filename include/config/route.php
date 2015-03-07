<?php

defined('LINKO') or exit();

/**
 * Core File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

Linko::Router()->add('/', array(
    'id' => 'page:index',
    'controller' => 'page/view'
));

Linko::Router()->add('install(/step-[step])', array(
    'id' => 'install',
    'controller' => 'install/install',
    'rules' => array(
        'step' => ':alpha'
    )
));

Linko::Router()->add('admincp(/[uri])', array(
    'id' => 'admincp',
    'controller' => 'admincp/index',
    'rules' => array(
        'uri' => '.*'
    )
));
	  
Linko::Router()->add('[slug]', array(
    'id' => 'page:view',
    'controller' => 'page/view',
    'rules' => array(
        'slug' => '.*'
    )
));