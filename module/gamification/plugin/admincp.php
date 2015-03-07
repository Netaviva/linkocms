<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage blog : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Gamification_Plugin_Admincp
{
    public function init()
    {
        // Posts
        Linko::Model('Admincp')->addRoute('gamification', array(
            'id' => 'gamification:admincp',
            'controller' => 'gamification/admincp/index',
        ));
               
        Linko::Model('Admincp')->addRoute('gamification/badge/[action]/[id]', array(
            'id' => 'gamification:admincp:badge',
            'controller' => 'gamification/admincp/badge',
            'rules' => array(
                'action' => 'edit|delete',
                'id' => ':int'
            )
        ));
        
        //menus
        $aMenu = array
        (
            'Manage Activities' => Linko::Url()->make('gamification:admincp'),
        );

        if(Linko::Model('Gamification/Point')->usePointSystem())
        {
          //     $aMenu['Edit Point System'] = Linko::Url()->make('gamfication:admincp:points');
        }
        Linko::Model('Admincp')->addMenu('Gamification', $aMenu);
    }
    
    // called in admincp/controller/dashboard
    public function con_dashboard()
    {

    }
}
?>