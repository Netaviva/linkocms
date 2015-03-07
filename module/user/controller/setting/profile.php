<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : setting\profile.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Setting_Profile extends Linko_Controller
{
    public function main()
    {
        // Only allow logged
        Linko::Model('User/Auth')->auth(true);

        $iUserId = Linko::Model('User/Auth')->getUserId();

        $aUser = Linko::Model('User')->getUser($iUserId);

        if($aVals = Input::post('val'))
        {
            $aValidate = array();

            Linko::Validate()->set('profile-setting', $aValidate);

            if(Linko::Validate()->isValid($aVals))
            {
                list($bReturn, $aUser) = Linko::Model('User/Action')->update($iUserId, $aVals);

                if($bReturn == true)
                {
                    Linko::Flash()->success(Lang::t('user.profile_updated'));
                    Linko::Response()->redirect('self');
                }
            }
        }

        Linko::Template()->setVars(array(
            'aCountries' => Linko::Model('Locale/Country')->getCountries(),
            'aUser' => $aUser,
        ), $this);
    }
}

?>