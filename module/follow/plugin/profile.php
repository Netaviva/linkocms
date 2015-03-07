<?php

defined('LINKO') or exit();

class Follow_Plugin_Profile
{
    public function tools_start()
    {
        Linko::Template()->setScript('follow.js', 'module_follow');
        echo Linko::Template()->getTemplate('follow/controller/profile/tools',null,true);
    }

    public function init()
    {

        Linko::Router()->add('interact/[slug](/[page])' , array(
            'id' => 'follow:profile:index',
            'controller' => 'follow/profile/index',
            'rules' => array
            (
                'page' => ':int',
                'slug' => 'follow|followers'
            )

        ));

        //add profile statisticts
        Linko::Model('profile')->addStatistic(array('link' => Linko::Model('profile')->buildUrl('interact/follow/'), 'name' => Lang::t('total-user-following'), 'number' => Linko::Model('follow')->count('follow')));
        Linko::Model('profile')->addStatistic(array('link' => Linko::Model('profile')->buildUrl('interact/followers/'), 'name' => Lang::t('total-user-followers'), 'number' => Linko::Model('follow')->count('followers')));

        //add to profile menus
        Linko::Model('profile')->registerMenu(Lang::t('following'), array('link' => Linko::Model('profile')->buildUrl('interact/follow/'),'id' => 'follow'));
        Linko::Model('profile')->registerMenu(Lang::t('followers'), array('link' => Linko::Model('profile')->buildUrl('interact/followers/'),'id' => 'followers'));
    }
}
?>