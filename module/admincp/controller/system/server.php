<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage admincp : system\server.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Admincp_Controller_System_Server extends Linko_Controller
{
	public function main()
	{
		$oConnection = Linko::Database()->getActiveConnection();
		
		$aServer = array(
			'Server Api (SAPI)' => php_sapi_name(),
			'Server Databases' => $oConnection->getDriver() . ' ' . $oConnection->getVersion(),
			'Server Software' => Linko::Request()->getServer('SERVER_SOFTWARE'),
			'Operating System (OS)' => PHP_OS . ' ' . php_uname('r') . ' On ' . php_uname('m'),
		);
		
		Linko::Template()->setTitle('Server Environment')
			->setBreadcrumb(array(
				'System Information',
				'Server',
			), 'Server Environment');
		
		Linko::Template()->setVars(array(
				'aServer' => $aServer,
			)
		);		
	}
}

?>