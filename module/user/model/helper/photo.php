<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : model - helper\photo.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class User_Model_Helper_Photo extends Linko_Model
{
    public function getPhoto($aParams)
    {
        /**
         * @var $user
         * @var $size
         */
        extract(array_merge(array(
            'user' => array(),
            'size' => 200,
        ), $aParams));

        $sPath = (isset($user['user_photo']) && $user['user_photo'] != null) ? $user['user_photo'] : 'no_photo_%d.png';

        $sUrl = Linko::Url()->path('storage/upload/user') . sprintf($sPath, $size);

        return $sUrl;
    }
}