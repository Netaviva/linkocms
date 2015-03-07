<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage comment : loader
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

Linko_Object::map('Template_Plugin_Comment', Linko::Config()->get('dir.module') . 'comment' . DS . 'include' . DS .  'template.plugin.comment.php');

Linko::Template()->registerPlugin('comment');

?>