
<?php
defined('LINKO') or exit();


/**

* @author LinkoDEV team

* @package linkocms

* @subpackage Gamification plugin : comment.php

* @version 1.0.0

* @copyright Copyright (c) 2013. All rights reserved.

*/

class Gamification_Plugin_Comment
     {

       public function add_comment()
       {


          $args = func_get_args();

           Linko::Model('Gamification')->gamify('user-post-comments',$args);

       }


/*endclass*/}
            