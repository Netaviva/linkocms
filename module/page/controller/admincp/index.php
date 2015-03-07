<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage page : admincp\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Page_Controller_Admincp_Index extends Linko_Controller
{
	public function main()
	{
		$aPages = Linko::Model('Page')->getPages();

        Linko::Template()->setBreadcrumb(array(
			'Page'
		), 'Page Manager')
        ->setTitle('Manage Pages')
		->setStyle('admincp.css', 'module_page')
        ->setScript('admin.js', 'module_page')
		->setVars(array(
				'aPages' => $aPages
			)
		);	
	}
}

?>