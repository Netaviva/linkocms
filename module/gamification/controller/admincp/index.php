<?php

class Gamification_Controller_Admincp_Index extends Linko_Controller
{
    public function main()
    {

        if($aVal = Input::post('val'))
        {

            if(Linko::Model('gamification/badge')->addBadges($aVal))
            {
                Linko::Flash()->success("Badge add successfully.");
                Linko::Response()->redirect('self');

            }else
            {
                Linko::Flash()->error('Failed to add badge, check your details');
            }
        }
        Linko::Template()
            ->setBreadcrumb(array(
            'Modules',
        ), 'Manage Gamification Activities')
            ->setTitle('Gamification Activities')
            ->setVars(array(
                'aActivities' => Linko::Model('Gamification')->getAvaliableActivities(),
            )
        )->setScript('admincp/gamification.js', 'module_gamification');

    }
}