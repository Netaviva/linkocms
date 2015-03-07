<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : controller admincp\role\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Admincp_Role_Index extends Linko_Controller
{
    public function main()
    {
        $aUserRoles = Linko::Model('User/Role')->getRoles();

        Linko::Template()->setBreadcrumb(array(
            'User' => Linko::Url()->make('user:admincp'),
            'User Roles' => null
        ), 'User Roles')
        ->setTitle('User Roles')
        ->setVars(array(
            'aUserRoles' => $aUserRoles
        ));
    }
}

?>
