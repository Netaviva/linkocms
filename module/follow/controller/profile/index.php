<?php

defined('LINKO') or exit();

class Follow_Controller_Profile_Index extends Linko_Controller
{
    public function main()
    {
        $sSlug = $this->getParam('slug');

        $iPagenumber = $this->getParam('page');

        list($iTotal, $aRow) = Linko::Model('follow')->getList(Linko::Model('profile')->getOwnerId(),$sSlug, $iPagenumber);
        $aPager = array
        (
            'total_items' => $iTotal,
            'current_page' => $iPagenumber,
            'rows_per_page' => Linko::Model('follow')->limit,
            'route_id' => 'user:profile',
            'route_param' => array( 'slug' =>$sSlug, 'username' => Linko::Model('profile')->get('username') )

        );

        Linko::Pager()->set($aPager);

        $aResult = array();
        foreach($aRow as $k)
        {
            $iUserId = ($sSlug =='follow') ? $k['user_id'] : $k['reference_id'];
            $aResult[] = Linko::Model('User')->getUser($iUserId);
        }

        Linko::Template()
                        ->setStyle('follow.css', 'module_follow')
                        ->setVars(array( 'current' => $sSlug, 'aResult' => $aResult));
    }
}