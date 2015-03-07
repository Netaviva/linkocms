<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage blog : admincp\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Blog_Controller_Admincp_Index extends Linko_Controller
{
    public function main()
    {
        $iPage = (int)$this->getParam('page');
        
        if($sAction = Input::post('post_action'))
        {
            $aIds = (array)Input::post('id');
                        
            switch($sAction)
            {
                case 'approve':
                    if(Linko::Model('Blog/Action')->approvePost(array_keys($aIds)))
                    {
                        Linko::Flash()->success('Selected post approved.');
                        Linko::Response()->redirect('self');
                    }
                    break;
                case 'unapprove':
                    if(Linko::Model('Blog/Action')->unapprovePost(array_keys($aIds)))
                    {
                        Linko::Flash()->success('Selected post approved.');
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
                
                Linko::Model('Blog/Action')->approvePost($iId, $bApprove);
            }
            
            Linko::Flash()->success('Posts Approved/Unapproved.');
            Linko::Response()->redirect('self'); 
        }
        
        list($iCount, $aPosts) = Linko::Model('Blog/Browse')
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
         
        Linko::Template()->setBreadcrumb(array(
			'Blog'
		), 'All Post')
		->setVars(array(
            'aPosts' => $aPosts,
            'iCount' => $iCount
		))
        ->setTitle('All Post');   
    }
}

?>