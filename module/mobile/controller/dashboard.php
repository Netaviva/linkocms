<?php defined('LINKO') or exit;

/**
 * @package Mobile
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class Mobile_Controller_Dashboard extends Linko_Controller
{
    public function main()
    {
        if(!Linko::Model('User/Auth')->isUser())
        {
            Linko::Response()->redirect('mobile:home');
        }

        Linko::Template()->setVars(array(
            'aItems' => Linko::Model('Mobile')->getDashboardItems()
        ));
    }
}