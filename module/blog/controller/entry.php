<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage blog : entry.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Blog_Controller_Entry extends Linko_Controller
{
	public function main()
	{
		$sSlug = $this->getParam('slug');
		
		$aPost = Linko::Model('Blog')->getPostBySlug($sSlug);

		Linko::Plugin()->filter('blog.controller_post_filter', $aPost);

		if(!count($aPost))
		{
			return Linko::Module()->set('_404_', array('message' => 'The post you are looking for does not exists.'));
		}

		$aVals = array();

		$iPostId = (int) $aPost['post_id'];

        if($this->getSetting('blog.enable_default_comment') && Linko::Module()->isModule('comment'))
        {
            Linko::Model('Comment')->init(); // comment
        }

		Linko::Template()->setVars(array(
				'aPost' => $aPost,
                'iPostId' => $iPostId,
                'aVals' => $aVals,
                'bCanComment' => (($this->getSetting('blog.members_only_comment') == true && Linko::Model('User/Auth')->isUser()) || $this->getSetting('blog.members_only_comment') == false)
		), $this)
		->setTitle($aPost['post_title'])
		->setBreadcrumb(array(
			'Blog' => Linko::Url()->make('blog:index'), 
			$aPost['post_title']
		));

        Linko::Plugin()->call('blog.controller_entry_end', $iPostId);
	}
}

?>