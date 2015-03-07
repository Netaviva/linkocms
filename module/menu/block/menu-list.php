<?php

class Menu_Block_Menu_List extends Linko_Controller
{
    public function main()
    {
        $iMenuId = $this->getParam('menu_id');
        $aMenu = Linko::Model('Menu')->getMenu($iMenuId);
        $aMenuItems = Linko::Model('Menu')->getMenuItems($iMenuId);

        Linko::Template()->setVars(array(
            'menu_id' => $iMenuId,
            'aMenuItems' => $aMenuItems
        ), $this);
    }
}