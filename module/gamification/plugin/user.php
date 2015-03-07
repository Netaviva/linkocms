<?php
defined('LINKO') or exit();


/**

 * @author LinkoDEV team

 * @package linkocms

 * @subpackage plugin : user.php

 * @version 1.0.0

 * @copyright Copyright (c) 2013. All rights reserved.

 */
class Gamification_Plugin_User
{
    public function gamify_login($sActivityId, $args)
    {
        $iUserId = $args[0];

        Linko::Model('gamification')->setUserId($iUserId);
    }

    public function gamify_register($aActivityId, $args)
    {
        $iUserId = $args[0];

        Linko::Model('gamification')->setUserId($iUserId);

    }



     public function login()
     {

         $args = func_get_args();

         Linko::Model('Gamification')->gamify('user-login',$args);

    }


              
     public function add_user()
     {

         $args = func_get_args();

         Linko::Model('Gamification')->gamify('user-register',$args);

    }

/*endclass*/}
              