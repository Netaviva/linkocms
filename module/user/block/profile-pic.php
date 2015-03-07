<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : block - profile-pic.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Block_Profile_Pic extends Linko_Controller
{
    public function main()
    {
        $aUser = Linko::Model('User/Auth')->getUser();

        $iSize = $this->getParam('picture_size');

        Linko::Template()->setVars(array(
            'aUser' => $aUser,
            'iSize' => $iSize
        ));
    }
}

?>