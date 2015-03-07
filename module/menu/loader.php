<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage menu : loader
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

Linko::Template()->registerPlugin('menu', dirname(__FILE__). DS . 'template' . DS . 'plugin' . DS . 'menu.php');

?>