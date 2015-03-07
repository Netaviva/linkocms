<?php

defined('LINKO') or exit();

class Gamification_Block_Point extends Linko_Controller
{
    public function main()
    {
        Linko::Template()
            ->setStyle('badge.css', 'module_gamification')
            ->setVars(
            array
            (
                'point' => Linko::Model('gamification/point')->get(Linko::Model('user/auth')->getUserId())
            )
        );

    }
}