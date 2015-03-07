<?php

class Activity_Plugin_Comment
{
	public function add_comment($iId, $aVals, $sModule, $iItemId)
	{
		/**
		if($sModule == 'activity')
		{
			Linko::Model('Activity/Action')->add(array(
				'module_id' => 'activity',
				'feed_var' => 'add_activity_comment',
				'item_id' => $iItemId // activity id
			));
		}
		/**/
	}
}