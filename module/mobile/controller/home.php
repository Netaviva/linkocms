<?php defined('LINKO') or exit;

/**
 * @package Mobile
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class Mobile_Controller_Home extends Linko_Controller
{
    public function main()
    {
        if(Linko::Model('User/Auth')->isUser())
        {
            Linko::Response()->redirect('mobile:dashboard');
        }
    }
}