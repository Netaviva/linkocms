<?php

class Activity_Model_Activity extends  Linko_Model
{
	public function getActivity($iId)
	{
		$mActivity = Linko::Database()->table('activity_feed', 'af')
			->select('af.*', Linko::Model('User')->getTableFields('u'))
			->leftJoin('user', 'u', 'u.user_id = af.user_id')
			->where('activity_id', '=', $iId)
			->query()
			->fetchRow();

		return $mActivity;
	}

    public function getForDisplay($mActivity)
    {
        if(is_int($mActivity))
        {
            $mActivity = $this->getActivity($mActivity);

	        if(!count($mActivity))
	        {
		        return array();
	        }
        }

        $sTask = 'activity_' . Inflector::underscore($mActivity['feed_var']);

        $mActivity['time_created_unix'] = $mActivity['time_created'];
        $mActivity['time_created'] = Date::timeAgo($mActivity['time_created']);

        if(Linko::Module()->hasTask($mActivity['module_id'], $sTask))
        {
            $mActivity = array_merge(array(
                'activity_image' => '',
                'activity_text' => null,
                'activity_template' => 'activity/block/_extra/activity-item',
                'activity_content_template' => 'activity/block/_extra/activity-item-content',
                'display_activity' => true,
            ), (Linko::Module()->hasTask($mActivity['module_id'], $sTask)) ? Linko::Module()->callTask($mActivity['module_id'], $sTask, $mActivity['item_id'], $mActivity) : array(), $mActivity);
        }
	    else
	    {
		    $mActivity = array_merge(array(
			    'activity_image' => '',
			    'activity_text' => null,
			    'display_activity' => false,
		    ), $mActivity);
	    }

        return $mActivity;
    }

    public function getCommentsForActivity($iId, $iLimit)
    {
        return Linko::Model('Comment')->getComments($iId, 'activity', $iLimit);
    }

    public function commentEnabled()
    {
        return (Linko::Module()->isModule('comment') && Linko::Module()->getSetting('activity.enable_comment'));
    }

    public function canComment()
    {
        return Linko::Model('User/Auth')->isUser();
    }
}