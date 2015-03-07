<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : admincp\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Admincp_Index extends Linko_Controller{	public function main()	{		$iPage = $this->getParam('page');		list($iTotalUsers, $aUsers) = Linko::Model('User/Browse')			->verified(false)			->get($iPage, 5);				Linko::Template()->setBreadcrumb(array(			'User'		), 'Browse Users')		->setVars(array(				'aUsers' => $aUsers			)		);				}}?>