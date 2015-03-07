<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage menu : ajax - ajax.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Menu_Ajax extends Linko_Ajax
{
    public function updateOrder()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return ;
        }
        
        $aOrder = Input::post('order');
        
        if(Linko::Model('Menu/Action')->updateOrder($aOrder))
        {
            $this->toJson($aOrder);
        }
    }
}

?>