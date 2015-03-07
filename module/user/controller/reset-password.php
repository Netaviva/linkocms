<?php

class User_Controller_Reset_Password extends Linko_Controller
{
	public function main()
	{
		$bResetPage = false;

		if($sKey = $this->getParam('key'))
		{
			$bResetPage = true;

			if($aVals = Input::post('val'))
			{
				$oValidate = Linko::Validate()->set(array(
					'password' => array(
						'function' => 'equal:' . $aVals['password_confirm'],
						'error' => Lang::t('user.both_password_must_match')
					)
				));

				if($oValidate->isValid($aVals))
				{
					$aRow = Linko::Database()->table('user')
						->select('user_id, username, password_reset_key, password_reset_time')
						->where('password_reset_key', '=', $sKey)
						->query()
						->fetchRow();

					if(!isset($aRow['user_id']))
					{
						return Linko::Error()->set(Lang::t('user.invalid_password_reset_key'));
					}

					if($iExpireTime = Linko::Module()->getSetting('user.password_request_expire_time'))
					{
						if(($aRow['password_reset_time'] + ($iExpireTime * 60 * 60)) < Date::now()) // expired
						{
							return Linko::Error()->set(Lang::t('user.password_reset_key_expired'));
						}
					}

					if(Linko::Model('User/Action')->resetPassword($aRow['user_id'], $aVals['password'], $sKey))
					{
						// After resetting password, re-loggin user
						Linko::Model('User/Auth')->logout();
						Linko::Model('User/Auth')->login($aRow['username'], $aVals['password'], false, 'username');

						// redirect to home page
						Linko::Flash()->success(Lang::t('user.password_changed'));
						Linko::Response()->redirect('');
					}
				}
			}
		}
		else
		{
			if($sEmail = Input::post('email'))
			{
				list($bReturn, $sMessage) = Linko::Model('User/Action')->resetPasswordRequest($sEmail);

				$sType = 'warning';

				if($bReturn)
				{
					$sType = 'success';
				}

				call_user_func(array(Linko::Flash(), $sType), $sMessage);
				Linko::Response()->redirect('self');
			}
		}

		Linko::Template()->setVars(array(
			'bResetPage' => $bResetPage
		));
	}
}