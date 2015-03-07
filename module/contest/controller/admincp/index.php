<?php

class Contest_Controller_Admincp_Index extends Linko_Controller
{
    public function main()
    {
        $iPage = 10; //(int)$this->getParam('page');
        
        if($sAction = Input::post('contest_action'))
        {
            $aIds = (array)Input::post('id');
                        
            switch($sAction)
            {
                case 'approve':
                    if(Linko::Model('Contest/Action')->approveContest(array_keys($aIds)))
                    {
                        Linko::Flash()->success('Selected Contest approved.');
                        Linko::Response()->redirect('self');
                    }
                    break;
                case 'unapprove':
                    if(Linko::Model('Contest/Action')->unapproveContest(array_keys($aIds)))
                    {
                        Linko::Flash()->success('Selected Contest unapproved.');
                        Linko::Response()->redirect('self');                        
                    }
                    break;
            }
        }
        
        if($aIds = Input::post('idss'))
        {
            $aApprove = array_keys((array) Input::post('approve'));
            
            foreach($aIds as $iId)
            {
                $bApprove = false;
                
                if(in_array($iId, $aApprove))
                {
                    $bApprove = true;
                }
                
                Linko::Model('Contest/Action')->approveContest($iId, $bApprove);
            }
            
            Linko::Flash()->success('Contests Approved/Unapproved.');
            Linko::Response()->redirect('self'); 
        }
        
        list($iCount, $aContests) = Linko::Model('Contest/Browse')
            ->approved(false)
            ->page($iPage)
            ->limit(15)
            ->get();
            
        Linko::Pager()->set(array(
            'current_page' => $iPage,
            'rows_per_page' => 15,
            'route_key' => 'page',
            'total_items' => $iCount
        ));
         
        Linko::Template()->setBreadcrumb(array(Lang::t('contest.sport')), 'All Sport Contests')
		->setVars(array(
            'aContests' => $aContests,
            'iCount' => $iCount
		))
        ->setTitle('All Contests'); 
    }


}
?>