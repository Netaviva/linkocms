<?php

defined('LINKO') or exit();

class Gamification_Plugin_Profile
{
    public function tools_start()
    {
        //Linko::Template()->setScript('follow.js', 'module_follow');
        echo Linko::Template()->getTemplate('gamification/block/profile/_extra/info',null,true);
    }

    public function init()
    {

        Linko::Router()->add('gamification/badges(/[page])' , array(
            'id' => 'gamifiation:profile:badges',
            'controller' => 'gamification/profile/badges',
            'rules' => array
            (
                'page' => ':int',

            )

        ));

        //add profile statisticts
        Linko::Model('profile')->addStatistic(array(
            'link' => Linko::Model('profile')->buildUrl('gamification/badges'),
            'name' => Lang::t('gamification.badges'),
            'number' => Linko::Model('Gamification/Badge')->total(Linko::Model('profile')->getOwnerId())));


        //add to profile menus
        //Linko::Model('profile')->registerMenu(Lang::t('following'), array('link' => Linko::Model('profile')->buildUrl('interact/follow/'),'id' => 'follow'));
       // Linko::Model('profile')->registerMenu(Lang::t('followers'), array('link' => Linko::Model('profile')->buildUrl('interact/followers/'),'id' => 'followers'));
    }
}
?>