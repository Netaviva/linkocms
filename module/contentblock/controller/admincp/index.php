<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage contentblock : admincp\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Contentblock_Controller_Admincp_Index extends Linko_Controller
{
	public function main()
	{
		$aBlocks = Linko::Model('Contentblock')->get();
		
		Linko::Template()->setBreadcrumb(array(
			'Content Blocks'
		), 'Content Blocks')
        ->setTitle('Manage Content Block')
		->setVars(array(
				'aBlocks' => $aBlocks
			)
		);
	}
}

?>