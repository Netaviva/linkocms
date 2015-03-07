<?php

class Activity_Block_Comment extends Linko_Controller
{
    public function main()
    {
	    $bShowAllComments = false;

	    if($this->getParam('bShowAllComments'))
	    {
			$bShowAllComments = true;
	    }

        $iActivity = (int)$this->getParam('activity_id');
	    $iLimit = (($bShowAllComments) ? 0 : 5);

        list($aComments, $iTotalComments) = Linko::Model('Activity')->getCommentsForActivity($iActivity, $iLimit);

        Linko::Template()->setVars(array(
	        'iActivity' => $iActivity,
            'aActivityComments' => $aComments,
            'iTotalComments' => $iTotalComments,
	        'bShowAllComments' => $bShowAllComments
        ));
    }
}