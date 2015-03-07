<?php

class Activity_Plugin_Blog
{
	public function add_post($iPostId)
	{
		// hook into blogs add_post plugin call
		Linko::Model('Activity/Action')->add(array(
			'module_id' => 'activity',
			'feed_var' => 'add_blog_post',
			'item_id' => $iPostId
		));
	}
}