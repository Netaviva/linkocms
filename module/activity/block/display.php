<?php

class Activity_Block_Display extends Linko_Controller
{
    public function main()
    {
        $bUser = false;
        $oActivityBrowse = Linko::Model('Activity/Browse');
        $iUser = $this->getParam('user_id');
        $iPage = max(intval($this->getParam('page')), 1);

        if($iUser)
        {
            $bUser = true;
        }

        $oActivityBrowse->setPage($iPage);

        if($bUser)
        {
            $oActivityBrowse->setUser($iUser);
        }

        $oActivityBrowse->process();

	    $aActivities = $oActivityBrowse->getActivities();

        Linko::Template()->setVars(array(
                'aActivities' => $aActivities,
                'bCommentEnabled' => Linko::Model('Activity')->commentEnabled(),
                'bCanComment' => Linko::Model('Activity')->canComment(),
	            'bUser' => $bUser,
	            'iUser' => $iUser
            ));
    }
}