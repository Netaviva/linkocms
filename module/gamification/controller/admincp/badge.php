<?php

class Gamification_Controller_Admincp_Badge extends Linko_Controller
{
    public function main()
    {

        $iId = $this->getParam('id');

        $sAction = $this->getParam('action');

        if($sAction == 'delete')
        {
            Linko::Model('Gamification/Badge')->delete($iId);
            Linko::Flash()->success("Badge deleted successfully.");
            Linko::Response()->redirect('gamification:admincp');
        }

        if($aVal = Input::post('val'))
        {

            if(Linko::Model('gamification/badge')->editBadge($aVal,$iId))
            {
                Linko::Flash()->success("Badge edited successfully.");
                Linko::Response()->redirect('self');

            }else
            {
                Linko::Flash()->error('Failed to edit badge, check your details');
            }
        }

        Linko::Template()
            ->setBreadcrumb(array(
            'Modules',
        ), 'Manage Gamification Badge')
            ->setTitle('Edit Badge')
            ->setVars(array(
                'aDetails' => Linko::Model('Gamification/Badge')->get($iId),
            )
        )->setScript('admincp/gamification.js', 'module_gamification');

    }
}