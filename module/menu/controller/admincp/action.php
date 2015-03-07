<?php

class Menu_Controller_Admincp_Action extends Linko_Controller
{
    public function main()
    {
        $sAction = $this->getParam('action');
        $iMenuId = $this->getParam('id');
        $bEdit = false;
        $aMenu = array();
        $aLocations = Linko::Model('Menu')->getLocations();
        $aMenus = array();

        Linko::Template()->setBreadcrumb(array('Manage Menus' => Linko::Url()->make('menu:admincp')))
            ->setTitle('Menus');

        switch($sAction)
        {
            case 'add':
                $bAdd = true;

                if($aVals = Input::post('val'))
                {
                    $oValidate = Linko::Validate()->set(array(
                        'title' => array('function' => 'required', 'error' => 'Must specify a menu title.')
                    ));

                    if($oValidate->isValid($aVals))
                    {
                        if(Linko::Model('Menu/Action')->addMenu($aVals))
                        {
                            Linko::Flash()->success('Menu Successfully Created.');
                            Linko::Response()->redirect('menu:admincp');
                        }
                    }
                }

                Linko::Template()->setBreadcrumb(array('Add Menu'), 'Add Menu')
                    ->setTitle('Add Menu');

                break;
            case 'edit':

                $bEdit = true;

                $aMenu = Linko::Model('Menu')->getMenu($iMenuId);

                Linko::Template()->setBreadcrumb(array('Edit Menu'), 'Edit Menu')
                    ->setTitle('Edit Menu');

                if($aVals = Input::post('val'))
                {
                    $oValidate = Linko::Validate()->set(array(
                        'title' => array('function' => 'required', 'error' => 'Must specify a menu title.')
                    ));

                    if($oValidate->isValid($aVals))
                    {
                        if(Linko::Model('Menu/Action')->updateMenu($iMenuId, $aVals))
                        {
                            Linko::Flash()->success('Menu Successfully Updated.');
                            Linko::Response()->redirect('menu:admincp');
                        }
                    }
                }

                break;
            case 'delete':
                $bDelete = true;

                if($iMenuId && (Linko::Model('Menu/Action')->deleteMenu($iMenuId)))
                {
                    Linko::Flash()->success('Menu Successfully Deleted.');
                    Linko::Response()->redirect('menu:admincp');
                }
                break;
        }

        Linko::Template()->setVars(array(
            'bEdit' => $bEdit,
            'aVals' => $aVals,
            'aMenu' => $aMenu,
            'aLocations' => $aLocations,
            'iMenuId' => $iMenuId
        ));
    }
}