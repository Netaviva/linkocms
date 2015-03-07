<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage admincp : error4.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Admincp_Controller_Error_404 extends Linko_Controller
{
	public function main()
	{
		Linko::Template()
			->setTitle('Error 404')
			->setBreadcrumb(array(
					'Error 404' => null
			), 'Error 404');	
	}
}

?>