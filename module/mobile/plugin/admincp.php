<?php defined('LINKO') or exit();

/**
 * @package Mobile
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class Mobile_Plugin_Admincp
{
    public function init()
    {
        Linko::Model('Admincp')->addRoute('mobile/dashboard', array(
            'id' => 'mobile:admincp:dashboard',
            'controller' => 'mobile/admincp/dashboard',
        ));

        Linko::Model('Admincp')->addMenu('Mobile', array(
            'Dashboard Manager' => Linko::Url()->make('mobile:admincp:dashboard')
        ));
    }
}

?>