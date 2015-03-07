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

/*
	Cache enable/disable
*/
Linko::Config()->set('Cache.enable', false);

/*
	Sets cache Key
	Will add prefix to cache names to avoid conflict with other 
	cache names especially in the case of memcache or apc
*/
Linko::Config()->set('Cache.prefix', 'linkocms');

/*
	Sets cache storage engine
	Supported memcache,file,apc,eaccelerator,php
*/
Linko::Config()->set('Cache.storage', 'file');

?>