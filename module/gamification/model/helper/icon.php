<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : model - helper\photo.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Gamification_Model_Helper_Icon extends Linko_Model
{
    public function getIcon($aParams)
    {
        /**
         * @var $user
         * @var $size
         */

        $sPath = str_replace('\\', '/', $aParams['path']);

        $sUrl = Linko::Url()->make(sprintf($sPath, $aParams['size']));

        return $sUrl;
    }
}