
<?php
defined('LINKO') or exit();


/**

* @author LinkoDEV team

* @package linkocms

* @subpackage plugin : activity.php

* @version 1.0.0

* @copyright Copyright (c) 2013. All rights reserved.

*/

class Gamification_Plugin_Activity
     {

       public function add_feed()
       {


          $args = func_get_args();

           Linko::Model('Gamification')->gamify('user-post-status',$args);

       }


/*endclass*/}
            