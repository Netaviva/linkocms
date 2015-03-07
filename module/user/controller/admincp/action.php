<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : admincp\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Admincp_Action extends Linko_Controller {

    /**
     * 
     * @return void
     */
    public function main() {
        $sAction = $this->getParam('action');
        $iId = $this->getParam('id');
        $aUser = array();
        $aVals = array();
        $bEdit = false;

        $aRules = array(
            'username' => array('function' => 'required', 'error' => 'Username cannot be empty'),
            'email' => array('function' => array('required', 'email'), 'error' => array('Email is required.', 'Invalid Email.'))
        );

        switch ($sAction) {
            case 'edit':
                $bEdit = true;
                $aRow = Linko::Model('User')->getUser($iId);

                if (!count($aRow)) {
                    Linko::Flash()->warning('Invalid User.');
                    Linko::Response()->redirect('user:admincp');
                }

                $aUser = array(
                    'username' => $aRow['username'],
                    'email' => $aRow['email']
                );

                $oValidate = Linko::Validate()->set('edit_user', $aRules);

                if (($aVals = Input::post('val')) && ($oValidate->isValid($aVals))) {
                    if (Linko::Model('User/Action')->update($aVals, $iId)) {
                        Linko::Flash()->success('User successfully updated.');
                        Linko::Response()->redirect('user:admincp');
                    }
                }
                break;
            case 'add':
                if ($aVals = Input::post('val')) {
                    $oValidate = Linko::Validate()->set(array(
                        'username' => array('function' => 'required', 'error' => 'Username cannot be empty'),
                        'email' => array('function' => array('required', 'email'), 'error' => array('Email is required.', 'Invalid Email.'))
                            ));
                }
                break;
        }


        Linko::Template()->setBreadcrumb(array(
                    'Edit User'
                        ), 'Edit Users')
                ->setVars(array(
                    'aUser' => $aUser,
                    'aVals' => $aVals,
                    'bEdit' => $bEdit,
                        )
        );
    }

}

?>