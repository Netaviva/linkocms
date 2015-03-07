<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage admincp : dashboard.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Admincp_Controller_Dashboard extends Linko_Controller
{
	public function main()
	{
        Linko::Plugin()->call('admincp.con_dashboard');
        
		Linko::Template()->setTitle('Dashboard')
		->setBreadcrumb(array(
			), 'Dashboard'
		)
        ->setStyle('dashboard.css', 'module_admincp', 'header');
	}
}

?>