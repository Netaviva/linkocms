<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class User_Model_Action extends Linko_Model
{
	public function __construct()
	{
		
	}

    /**
     * Adds a new User
     *
     * @param array $aVals
     * @param int $iUserId
     * @return array
     */
	public function add(array $aVals, $iUserId = null)
	{
        $bUpdate = false;

        if($iUserId)
        {
            $bUpdate = true;
        }

        if($bUpdate == false)
        {
            // if we are inserting a new user and these doesnt exists. what are we inserting?
            if(!Arr::hasKeys($aVals, 'password', 'username', 'email'))
            {
                return false;
            }

            // if not updating user, lets make sure these keys exists in the vals array
            $aVals = array_merge(array(
                'activated' => false,
                'role_id' => CMS::USER_ROLE_USER,
                'locale_id' => 'en_GB',
                'time_zone' => null,
                'time_dst_check' => null
            ), $aVals);
        }

        // if we want to update or add these, lets make sure it not being used
        if(Arr::hasKeys($aVals, 'username', 'email'))
        {
            $sCond = "(username = :username OR email = :email)";

            $aParam = array(':username' => $aVals['username'], ':email' => $aVals['email']);

            // if a user is updating and re-inputs his original email/username,
            // lets skip that to avoid...
            // System: "this username is already in use."...
            // User: (Angry) "By Who?" (Punch!).
            if($bUpdate)
            {
                $sCond .= " AND user_id != :user_id";

                $aParam[':user_id'] = $iUserId;
            }

            $bExists = Linko::Database()->table('user')
                ->select('user_id')
                ->where($sCond)
                ->param($aParam)
                ->query()
                ->getCount();

            if($bExists)
            {
                return Linko::Error()->set(Lang::t('user.user_with_that_name_or_email_already_exists'));
            }
        }

        // these checks are here so when updating, all fields may not have to be forcibly defined

        if(array_key_exists('username', $aVals))
        {
            $aVals['username'] = strtolower(str_replace(' ', '_', $aVals['username']));

            if(Linko::Model('User')->isUsernameAllowed($aVals['username']) == false)
            {
                return Linko::Error()->set(Lang::t('user.cannot_use_username'));
            }

            $aUser['username'] = $aVals['username'];
        }

        if(array_key_exists('email', $aVals))
        {
            $aUser['email'] = $aVals['email'];
        }

        if(array_key_exists('password', $aVals))
        {
            $aUser['password'] = Linko::Hasher()->hash($aVals['password']);
        }

        if(isset($aVals['role_id']))
        {
            $aUser['role_id'] = $aVals['role_id'];
        }

        if(isset($aVals['locale_id']))
        {
            $aUser['locale_id'] = $aVals['locale_id'];
        }

        if(isset($aVals['activated']))
        {
            $aUser['activated'] = $aVals['activated'];
        }

        if(isset($aVals['timezone']))
        {
            $aUser['time_zone'] = $aVals['timezone'];
        }

        if(isset($aVals['dst_check']))
        {
            $aUser['time_dst_check'] = $aVals['dst_check'];
        }

        if(isset($aVals['country_id']))
        {
            $aUser['country_id'] = $aVals['country_id'];
        }

        if(isset($aVals['birthday']))
        {
            $aUser['birthday'] = $aVals['birthday'];
        }

        if(isset($aVals['gender']))
        {
            $aUser['gender'] = $aVals['gender'];
        }

        if($bUpdate)
        {
            Linko::Plugin()->call('user.before_update_user', $iUserId, $aVals);

            // updating user
            Linko::Database()->table('user')
                ->update($aUser)
                ->where('user_id', '=', $iUserId)
                ->query();

            $aUserData = array();

            if(isset($aVals['firstname']))
            {
                $aUserData['firstname'] = $aVals['firstname'];
            }

            if(isset($aVals['lastname']))
            {
                $aUserData['lastname'] = $aVals['lastname'];
            }

            if(count($aUserData))
            {
                // only update the user_data table if there is something to update
                Linko::Database()->table('user_data')
                    ->update($aUserData)
                    ->where('user_id', '=', $iUserId)
                    ->query();
            }

            Linko::Plugin()->call('user.update_user', $iUserId, $aVals);

            return array($iUserId, $aUser);
        }
        else
        {
            Linko::Plugin()->call('user.before_add_user', $iUserId, $aVals);

            // creating user

            $aUser['time_joined'] = Date::now();

            if(Linko::Module()->getSetting('user.verify_account_on_signup'))
            {
                $sHash = sha1(uniqid(mt_rand())); // generate activation hash

                $aUser['activated'] = 0;
                $aUser['activation_hash'] = $sHash;
            }
            else
            {
                $sHash = '';
                $aUser['activated'] = 1;
            }

            $iUserId = Linko::Database()->table('user')
                ->insert($aUser)
                ->query()
                ->getInsertId();

            Linko::Database()->table('user_data')
                ->insert(array(
                    'user_id' => $iUserId,
                    'firstname' => isset($aVals['firstname']) ? $aVals['firstname'] : null,
                    'lastname' => isset($aVals['lastname']) ? $aVals['lastname']: null,
                ))
                ->query();

            if($iUserId && Linko::Module()->getSetting('user.verify_account_on_signup'))
            {
                $bSent = Linko::Model('Mail')->load('user.account_verification')
                    ->setParam(array(
                        'verification_link' => Linko::Url()->make('user:verify-account', array('hash' => $sHash))
                    ))
                    ->setTo($aUser['email'])
                    ->send();
            }
            else
            {

            }

	        // send welcome mail to user
	        Linko::Model('Mail')->load('user.welcome')
		        ->setTo($aUser['email'])
	            ->setParam(array('username' => $aUser['username']))
		        ->send();

            Linko::Plugin()->call('user.add_user', $iUserId, $aVals);

            return array($iUserId, $aUser);
        }
	}
    
    /**
     * Updates a User
     * 
     * @param array $aVals
     * @param int $iUserId
     * @return array
     */
    public function update($iUserId, $aVals)
    {
        return $this->add($aVals, $iUserId);
    }
    
    public function updateField($iUserId, $sField, $sValue)
    {
        Linko::Database()->table('user')
	        ->update(array($sField => $sValue))
	        ->where('user_id', '=', $iUserId)
	        ->query()
	        ->getAffectedRows();
        
        return true;
    }

	public function resetPasswordRequest($sEmail)
	{
		$aRow = Linko::Database()->table('user')
			->select('user_id, username, password_reset_key, password_reset_time')
			->where('email', '=', $sEmail)
			->query()
			->fetchRow();

		if(!isset($aRow['user_id']))
		{
			return array(false, Lang::t('user.no_record_with_email_found'));
		}

		$iExpireTime = Linko::Module()->getSetting('user.password_request_expire_time');
		$sKey = uniqid() . str_pad(rand(000, 999), 3, 8);

		if($aRow['password_reset_time'] && $aRow['password_reset_key'] != null)
		{
			if($iExpireTime && ((($iExpireTime * 60 * 60) + $aRow['password_reset_time'])) >= Date::now())
			{
				return array(false, Lang::t('user.already_requested_for_password_reset'));
			}
		}

		$bSent = Linko::Model('Mail')->load('user.password_reset_request')
			->setParam(array(
				'expire_time' => $iExpireTime,
				'reset_link' => Linko::Url()->make('user:reset-password', array('key' => $sKey))
			))
			->setTo($sEmail)
			->send();

		if($bSent)
		{
			Linko::Database()->table('user')
				->update(array(
					'password_reset_key' => $sKey,
					'password_reset_time' => Date::now()
				))
				->where('user_id', '=', $aRow['user_id'])
				->query();

			return array(true, Lang::t('user.password_reset_sent_to_email', array('expire_time' => $iExpireTime)));
		}
		else
		{
			return array(false, Lang::t('user.error_sending_mail'));
		}

		return array(false, 'Unknown Error!');
	}

	public function resetPassword($iUser, $sPassword, $sKey)
	{
		$sPassword = Linko::Hasher()->hash($sPassword);

		Linko::Database()->table('user')
			->update(array(
				'password' => $sPassword,
				'password_reset_key' => null,
				'password_reset_time' => null
			))
			->where('user_id', '=', $iUser)
			->where('password_reset_key', '=', $sKey)
			->query();

		return true;
	}

    public function changePassword($iUser, $sOldPass, $sNewPass)
    {
        $aUser = Linko::Model('User')->getUser($iUser, array('password'));

        // Verify if the old pass is correct
        if(Linko::Hasher()->compare($sOldPass, $aUser['password']) === false)
        {
            return array(false, Lang::t('user.incorrect_password'));
        }

        Linko::Database()->table('user')
            ->update(array(
                'password' => Linko::Hasher()->hash($sNewPass)
            ))
            ->where('user_id', '=', $iUser)
            ->query();

        // logout the user
        Linko::Model('User/Auth')->logout();

        // login the user with the new password
        Linko::Model('User/Auth')->login($aUser['username'], $sNewPass);

        return array(true, Lang::t('user.password_changed'));
    }

    public function verifyAccountByHash($sHash)
    {
        return Linko::Database()->table('user')
            ->update(array('activated' => 1, 'activation_hash' => ''))
            ->where('activation_hash', '=', $sHash)
            ->query()
            ->getAffectedRows();
    }
}