<?php

class Gamification_Controller_Profile_Badges extends Linko_Controller
{
    public function main()
    {

        Linko::Template()
                        ->setTitle(Lang::t('gamification.my-badges'))
                        ->setStyle('badge.css', 'module_gamification')
                        ->setVars(
                            array(
                                'aBadges' => Linko::Model('gamification/badge')->getUserBadges(Linko::Model('profile')->getOwnerId()))
                            );
    }
}