<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage menu : admincp\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Menu_Controller_Admincp_Index extends Linko_Controller
{
	public function main()
	{
        $bMenuItems = false;
        $iMenu = $this->getParam('menu_id');
        $aMenuItems = array();
		$aMenu = Linko::Model('Menu')->getMenu($iMenu);

        if($iMenu)
        {
            $bMenuItems = true;

            $aMenuItems = Linko::Model('Menu')->getMenuItems($iMenu);
        }

        if(Input::post('update_order') && $aIds = (Input::post('id')))
        {
            if(Linko::Model('Menu/Action')->updateOrder($aIds))
            {
                Linko::Flash()->success('Menu Order Updated Successfully.');
                Linko::Response()->redirect('self');
            }
        }
        
        Linko::Template()->setTitle('Manage Menu')
            ->setBreadcrumb(array(

            ), 'Manage Menu')
            ->setScript('admin.js', 'module_menu')
            ->setVars(array(
                'aMenus' => $aMenu,
                'aMenu' => $aMenu,
                'aMenuItems' => $aMenuItems,
                'bMenuItems' => $bMenuItems
            ));
	}
}

?>