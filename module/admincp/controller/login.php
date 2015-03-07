<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage admincp : login.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Admincp_Controller_Login extends Linko_Controller
{
    public function main()
    {
        Arr::hasKeys(array('username' => 'morrelinko', 'password' => 'cool'), 'username', 'password');
        
        Linko::Template()
        ->setBreadcrumb('Login')
        ->setTitle('Login')
        ->setLayout('login');
    }
}

?>