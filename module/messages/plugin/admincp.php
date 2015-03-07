<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage blog : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Messages_Plugin_Admincp
{
    public function init()
    {
        // Posts
        Linko::Model('Admincp')->addRoute('messages/send', array(
            'id' => 'messages:admincp:send',
            'controller' => 'messages/admincp/send',
        ));
               

        //menus
        $aMenu = array
        (
            'Send Messages' => Linko::Url()->make('messages:admincp:send'),
        );

        Linko::Model('Admincp')->addMenu('Messages', $aMenu);
    }
    
    // called in admincp/controller/dashboard
    public function con_dashboard()
    {

    }
}
?>