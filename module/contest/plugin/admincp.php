<?php

defined('LINKO') or exit();

/**
 * @author Stanley Ojadovwa
 * @package linkocms
 * @subpackage contest : plugin - admincp.php
 * @version 1.0.0
 * @copyright Netaviva (c) 2013. All Rights Reserved.
 */
class Contest_Plugin_Admincp {

    public function init() {
        Linko::Model('Admincp')->addRoute('contest(/[page])', array(
            'id' => 'contest:admincp',
            'controller' => 'contest/admincp/index',
            'rules' => array(
                'page' => ':int'
            )
        ));

        Linko::Model('Admincp')->addRoute('contest/[action](/[id])', array(
            'id' => 'contest:admincp:action',
            'controller' => 'contest/admincp/action',
            'rules' => array(
                'id' => ':int',
                'action' => 'add|edit|delete'
            )
        ));

        Linko::Model('Admincp')->addRoute('contest/history', array(
            'id' => 'contest:admincp:history',
            'controller' => 'contest/admincp/history/index'
        ));

        Linko::Model('Admincp')->addRoute('contest/history/[action](/[id])', array(
            'id' => 'contest:admincp:history:action',
            'controller' => 'contest/admincp/history/action',
            'rules' => array(
                'id' => ':int',
                'action' => 'add|edit|delete'
            )
        ));

        Linko::Model('Admincp')->addMenu('Sport Contest', array(
            'Sport Contest' => Linko::Url()->make('contest:admincp'),
            'Contest History' => Linko::Url()->make('contest:admincp:history')
        ));
    }

}

?>