<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage page : error4.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Page_Controller_Error_404 extends Linko_Controller
{
	public function main()
	{
	   // Message to be displayed to user
	   $sMessage = $this->getParam('message');
       
	   Linko::Template()
			->setTitle('Error 404')
			->setBreadcrumb(array(), 'Error: Page Not Found')
            ->setVars(array(
                'sMessage' => $sMessage
            ));
	}
}

?>