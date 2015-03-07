<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage blog : block - recent-post.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Blog_Block_Recent_Post extends Linko_Controller
{
	public function main()
	{
		$aPosts = Linko::Model('Blog')->getRecentPosts();
		
		Linko::Template()->setVars(array(
				'aPosts' => $aPosts,	
			)
		);
	}
}

?>