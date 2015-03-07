<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : browse.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Browse extends Linko_Controller
{
	public function main()
	{
        $iPage = $this->getParam('page');
        
		list($iTotalUsers, $aUsers) = Linko::Model('User/Browse')
            ->verified(true)
            ->get($iPage, 3);
        
        Linko::Pager()->set(array(
            'route_id' => Linko::Router()->getId(),
            'rows_per_page' => 3,
            'route_key' => 'page',
            'total_items' => $iTotalUsers,
            'current_page' => $iPage
        ));
		
		Linko::Template()->setVars(array(
            'iTotalUsers' => $iTotalUsers,
            'aUsers' => $aUsers,
		));
	}
}

?>