<?php


class Activity_Ajax extends Linko_Ajax
{
    public function addStatus()
    {
        $aVals = Str::parseArray($this->getParam('val'));

	    if(empty($aVals['status_text']))
	    {
			return $this->toJson(array(
				'error' => true,
				'message' => Lang::t('activity.cannot_post_empty_message')
			));
	    }

        $aStatus = array('status' => $aVals['status_text']);

        if($iStatusId = Linko::Model('Activity/Status/Action')->add($aStatus))
        {
            $aActivity = array(
                'module_id' => 'activity',
                'feed_var' => 'add_status',
                'item_id' => $iStatusId
            );

            if($iActivityId = Linko::Model('Activity/Action')->add($aActivity))
            {
                $aActivity = Linko::Model('Activity')->getForDisplay($iActivityId);

                $sBlock = Linko::Template()->getTemplate($aActivity['activity_template'], array(
                    'aActivity' => $aActivity,
                    'bCommentEnabled' => Linko::Model('Activity')->commentEnabled(),
                    'bCanComment' => Linko::Model('Activity')->canComment(),
                ), true);

                $this->output($sBlock);
            }
        }
    }

	public function loadMore()
	{
		$iPage = (int)$this->getParam('page');
        $iUser = $this->getParam('user_id');

        /**
         * Perform the activity browse to see if there's no more activity to load
         */
        $oActivityBrowse = Linko::Model('Activity/Browse')->setPage($iPage);

		if($iUser != null)
		{
			$oActivityBrowse->setUser($iUser);
		}

		$iTotal = count($oActivityBrowse->process()->getActivities());

        if($iTotal == 0)
        {
            return $this->output('');
        }

        $this->output(Linko::Module()->getBlock('activity/display', array('user_id' => $iUser, 'page' => $iPage)));
	}

    public function addComment()
    {
        $sComment = $this->getParam('comment');
        $iActivityId = $this->getParam('activity_id');

	    if(empty($sComment))
	    {
		    return $this->toJson(array(
			    'error' => true,
			    'message' => Lang::t('activity.cannot_post_empty_comment')
		    ));
	    }

        if($iCommentId = Linko::Model('Activity/Action')->addComment($iActivityId, $sComment))
        {
            $aComment = Linko::Model('Comment')->getComment($iCommentId);

            $sCommentBlock = Linko::Template()->getTemplate('activity/block/_extra/comment-item', array('aComment' => $aComment));

            $this->output($sCommentBlock);
        }
    }
}
?>