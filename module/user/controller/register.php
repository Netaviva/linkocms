<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : register.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Register extends Linko_Controller
{
    public function main()
    {
        Linko::Plugin()->call('user.controller_register_start');

        $aVals = array();

        if(($aVals = Input::post('val')) && $this->getSetting('user.allow_user_registeration'))
        {
            $aValidate = array(
                'username' => array('function' => 'required', 'error' => 'Username is required.'),
                'email' => array('function' => 'email', 'error' => 'Email is not valid.'),
                'password' => array('function' => 'required', 'error' => Lang::t('user.password_cannot_be_empty'))
            );

            if($this->getSetting('user.re_enter_email_on_signup'))
            {
                $aValidate['confirm-email'] = array(
                    'function' => 'equal:' . $aVals['email'],
                    'error' => 'Your email and confirmation email must match.'
                );
            }

            if($this->getSetting('user.re_enter_password_on_signup'))
            {
                $aValidate['confirm-password'] = array(
                    'function' => 'equal:' . $aVals['password'],
                    'error' => 'Your password and confirmation password must match.'
                );
            }

            if($this->getSetting('user.enable_dob_on_signup'))
            {
                $aVals = array_merge(array(
                    'dob_month' => 0,
                    'dob_day' => 0,
                    'dob_year' => 0
                ), $aVals);

                if(!checkdate((int)$aVals['dob_month'], (int)$aVals['dob_day'], (int)$aVals['dob_year']))
                {
                    Linko::Error()->set("Invalid Date of birth selected.");
                }
            }

            Linko::Validate()->set('register-user', $aValidate);

            Linko::Plugin()->call('user.controller_register_before_validate');

            if(Linko::Validate()->isValid($aVals))
            {
                if($this->getSetting('user.enable_dob_on_signup'))
                {
                    $aVals['birthday'] = DateTime::createFromFormat('n-j-Y', $aVals['dob_month'] . '-' . $aVals['dob_day'] . '-' . $aVals['dob_year'])->getTimestamp();

                    unset(
                        $aVals['dob_month'], $aVals['dob_day'], $aVals['dob_year']
                    );
                }

                list($iUserId, $aUser) = Linko::Model('User/Action')->add($aVals);

                if($iUserId)
                {
                    $sRedirect = 'user:login';

                    if($this->getSetting('user.auto_login_after_signup'))
                    {
                        Linko::Model('User/Auth')->login($aVals['username'], $aVals['password']);

                        $sRedirect = '';
                    }

                    if($iPage = $this->getSetting('user.page_redirect_after_signup'))
                    {
                        $aPage = Linko::Model('Page')->getPage((int)$iPage);

                        $sRedirect = Linko::Url()->make(($aPage['route_id'] ? $aPage['route_id'] : $aPage['page_url']));
                    }

                    Linko::Plugin()->call('user.controller_register_success');

                    $sMsg = Lang::t('user.successfully_registered');

                    if($this->getSetting('user.verify_account_on_signup'))
                    {
                        $sMsg .= Lang::t('user.you_need_to_verify_your_account_to_access_all');
                    }

                    Linko::Flash()->success($sMsg);
                    Linko::Response()->redirect($sRedirect);
                }
            }
            else
            {
                Linko::Plugin()->call('user.controller_register_failed');
            }
        }
        
        Linko::Template()->setVars(array(
            'aVals' => $aVals
        ));
    }
}