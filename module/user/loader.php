<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : loader
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

define('DIR_USER_PHOTO', DIR_UPLOAD . 'user' . DS);

// Adds an alias for retrieving user image from path aliases feature in the template class
// Used In View/Layout Files
//
// $user = array('user_photo' => '2013/10/morrelinko_%d.png');
//
// $this->getImage(array('user' => $user, 'size' => '200'), 'user_photo');
//

Linko::Template()->setPathAlias('user_image', array(Linko::Model('User/Helper/Photo'), 'getPhoto'));

//