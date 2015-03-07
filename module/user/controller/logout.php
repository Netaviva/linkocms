<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : logout.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Logout extends Linko_Controller
{
	public function main()
	{
		if(Linko::Model('User/Auth')->logout())
		{
			Linko::Flash()->success('You have logged out.');
			Linko::Response()->redirect();
		}
	}
}

?>