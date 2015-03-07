<?php

class Activity_Model_Task extends Linko_Model
{
    public function activity_add_status($iId, $aFeed)
    {
        $aStatus = Linko::Model('Activity/Status')->getStatus($iId);

        $aRow = array();

        if(!count($aStatus))
        {
            // Ok, this activity is invalid. Lets delete it
            Linko::Model('Activity/Action')->delete($aFeed['activity_id']);

            $aRow['display_activity'] = false;

            return;
        }

        return array(
            'activity_image' => Linko::Template()->getImage('user_feed.png', 'module_activity'),
            'activity_text' => nl2br($aStatus['status'])
        );
    }

	public function activity_add_activity_comment($iId, $aFeed)
	{
		$aActivity = Linko::Model('Activity')->getActivity($iId);

		$aRow = array();

		if(!count($aActivity))
		{
			$aRow['display_activity'] = false;

			return;
		}

		$sText = Lang::t('activity.posted_a_comment_on_userlink_s_activitylink', array(
			'user_link'	=> Html::link($aActivity['username'], Linko::Url()->make('user:profile', array('username' => $aActivity['username']))),
			'activity_link' => Html::link('activity', Linko::Url()->make('activity:view', array('id' => $iId)))
		));

		return array(
			'activity_image' => Linko::Template()->getImage('user_feed.png', 'module_activity'),
			'activity_text' => $sText
		);
	}

}