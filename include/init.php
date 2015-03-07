<?php
/**
 * Core File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

define('ROOT_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

define('APP_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

define('INCLUDE_DIR', APP_PATH . 'include' . DIRECTORY_SEPARATOR);

define('FRAMEWORK_DIR', INCLUDE_DIR . 'framework' . DIRECTORY_SEPARATOR);

// Framework
require FRAMEWORK_DIR . 'loader.php';

require INCLUDE_DIR . 'config/cache.php';

require INCLUDE_DIR . 'config/route.php';

if(File::exists(INCLUDE_DIR . 'config/database.php'))
{
	require INCLUDE_DIR . 'config/database.php';
}

require INCLUDE_DIR . 'config/application.php';

require INCLUDE_DIR . 'cms.php';