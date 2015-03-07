<?php

class Gamification_Block_Badges extends Linko_Controller
{
    public function main()
    {

        $iUserId = $this->getParam('badges_user_id');

        if($iUserId == 'current-login-user')
        {
            $iUserId = Linko::Model('user/auth')->getUserId();
        }else
        {
            if(Linko::Module()->isModule('profile'))
            {
                $iUserId = Linko::Model('profile')->getOwnerId();
            }
        }


        Linko::Template()
            ->setStyle('badge.css', 'module_gamification')
            ->setVars(
            array
            (
                'aBadges' => Linko::Model('gamification/badge')->getUserBadges($iUserId,$this->getParam('badges_limit'))
            )
        );

    }
}