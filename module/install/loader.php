<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage install : loader
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

define('LINKOCMS_INSTALLER', true);

if(!Linko::Model('install')->isInstalled())
{
	if(Linko::Url()->segment(1) != 'install')
	{
		//Linko::Response()->redirect(Linko::Url()->make('install', array('step' => Linko::Model('install')->getFirstStep())));
	}
}

?>