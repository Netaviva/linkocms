<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : setting\account.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Setting_Account extends Linko_Controller
{
    public function main()
    {
        // Only allow authenticated users
        Linko::Model('User/Auth')->auth(true);

        $iUserId = Linko::Model('User/Auth')->getUserId();

        if(Input::post('change-password'))
        {
            if($aVals = Input::post('val'))
            {
                $aValidate = array(
                    'password' => array(
                        'function' => 'required',
                        'error' => Lang::t('user.password_cannot_be_empty')
                    ),
                    'confirm_password' => array(
                        'function' => 'equal:' . $aVals['password'],
                        'error' => Lang::t('user.both_password_must_match')
                    ),
                );

                Linko::Validate()->set('change-password', $aValidate);

                if(Linko::Validate()->isValid($aVals))
                {
                    list($bReturn, $sMsg) = Linko::Model('User/Action')->changePassword($iUserId, $aVals['old_password'], $aVals['password']);

                    if($bReturn)
                    {
                        Linko::Flash()->success($sMsg);
                        Linko::Response()->redirect('self');
                    }
                    else
                    {
                        Linko::Flash()->success(Lang::t('user.error_changing_password') . ' ' . $sMsg);
                        Linko::Response()->redirect('self');
                    }
                }
            }
        }

        if(Input::post('update-account'))
        {
            if($aVals = Input::post('val'))
            {
                $aValidate = array(
                    'username' => array(
                        'function' => 'required',
                        'error' => Lang::t('user.username_is_required')
                    ),
                    'email' => array(
                        'function' => 'required',
                        'error' => Lang::t('user.email_is_required')
                    ),
                );

                Linko::Validate()->set('account-setting', $aValidate);

                if(Linko::Validate()->isValid($aVals))
                {
                    list($bReturn, $aUser) = Linko::Model('User/Action')->update($iUserId, $aVals);

                    if($bReturn == true)
                    {
                        Linko::Flash()->success(Lang::t('user.account_updated'));
                        Linko::Response()->redirect('self');
                    }
                }
            }
        }

        Linko::Template()->setVars(array(
            'aUser' => Linko::Model('User')->getUser($iUserId),
            'aCountries' => Linko::Model('Locale/Country')->getCountries(),
            'aLanguages' => Linko::Model('Locale/Language')->getLanguages(),
            'aTimezones' => Linko::Model('Locale/Date')->getTimezones(),
        ));
    }
}

?>