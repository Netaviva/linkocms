<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : profile.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Profile extends Linko_Controller
{
	public function main()
	{
        define('USER_PROFILE', true);
        
        // Override page dissallow access. Non users are redirected to login
        Linko::Model('User/Auth')->auth(true);
        
		$sUser = $this->getParam('username');

        if($sUser == 'me')
        {
            // Nice! 'me' rep current user, lets get the current username;
            $sUser = Linko::Model('User/Auth')->getUserBy('username');
        }

		$aUser = Linko::Model('User')->getUser($sUser, array('role', 'language', 'country'));

        if(!isset($aUser['user_id']))
        {
            return Linko::Module()->set('_404_', array(
	            'message' => 'This user does not exist.'
            ));
        }

        $this->setGlobalParam('aUser', $aUser);

        $aDetails = array(
            Lang::t('user.role') => $aUser['role_title'],
            Lang::t('user.username') => $aUser['username'],
            Lang::t('user.fullname') => $aUser['lastname'] . ' ' . $aUser['firstname']
        );

        if($this->getSetting('user.display_email_on_profile'))
        {
            $aDetails[ Lang::t('user.email')] = $aUser['email'];
        }

        $aDetails['Language'] = $aUser['language_title'];
        $aDetails['Joined'] = Date::getTime(Linko::Config()->get('date.format'), $aUser['time_joined']);
        $aDetails[Lang::t('user.gender')] = Linko::Model('User')->getGender($aUser['gender']);
        $aDetails[Lang::t('user.location')] = $aUser['country_title'];

        if(Linko::Template()->isLayout('profile'))
        {
            Linko::Template()->setLayout('profile');
        }

		Linko::Template()->setVars(array(
				'aUser' => $aUser,
                'aDetails' => $aDetails
			)
		);
	}
}

?>