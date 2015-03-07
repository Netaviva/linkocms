<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage messages : loader
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

Linko::Template()
    ->setStyle('message_notify.css', 'module_messages')
    ->setScript('message_notify.js', 'module_messages')
    ->registerPlugin('messages', Linko::Config()->get('dir.module') . 'messages' . DS . 'include' . DS . 'template' . DS . 'plugin.php');

?>