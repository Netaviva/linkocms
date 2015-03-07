<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : model - auth.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class User_Model_Auth extends Linko_Model
{
	private $_iUser;
	
	private $_aUser = array();
	
	public function __construct()
	{
		$this->_iUser = Linko::Session()->get('user_id') ? Linko::Session()->get('user_id') : 0;
		$sHash = Linko::Session()->get('user_hash') ? Linko::Session()->get('user_hash') : null;
		
		if($this->_iUser)
		{
			$sSelect = 'u.user_id, u.username, u.password, u.email, u.user_photo, u.role_id, u.locale_id, u.country_id, u.login_hash, u.time_zone, u.time_dst_check, u.time_joined, u.activated';
			
			$this->_aUser = Linko::Database()->table('user', 'u')
                ->select($sSelect)
				->where("u.user_id", '=', $this->_iUser)
				->query()->fetchRow();

			if(!isset($this->_aUser['user_id']))
			{
				$this->logout();
				$this->_setVisitor();
			}

            // check if the user has verified his account.
            if($this->isVerified() == false)
            {
                $this->logout();
                $this->_setVisitor();
            }
			
			if($sHash != $this->_aUser['login_hash'])
			{
				$this->logout();
				$this->_setVisitor();					
			}

			$this->_aUser['password'] = null;
		}
		else
		{
			$this->_setVisitor();	
		}
	}
	
	public function isUser($bRedirect = false)
	{
		$bIsUser = ((isset($this->_aUser['user_id'])) && ($this->_aUser['user_id'] != 0)) ? true : false;

		if((!$bIsUser) && ($bRedirect))
		{
			Linko::Response()->redirect();
		}
		
		return $bIsUser;
	}

    public function isVerified()
    {
        if(!$this->isUser())
        {
            return false;
        }

        return ($this->_aUser['activated'] > 0) ? true : false;
    }

	public function auth($bRedirect = false)
	{
		return $this->isUser($bRedirect);
	}

    public function getUser()
    {
        return $this->_aUser;
    }

	public function getUserId()
	{
		return $this->_iUser;
	}
		
	public function getUserBy($sVar)
	{
		return isset($this->_aUser[$sVar]) ? $this->_aUser[$sVar] : null;
	}
	
	public function login($sLogin, $sPassword, $bRemember = false, $sType = 'username', $bVerifyPassword = true)
	{
        $sCond = null;
        
        switch($sType)
        {
            case 'username':
                $sCond = "username = :login";
                break;
            case 'email':
                $sCond = "email = :login";
                break;
            case 'both':
                $sCond = "username = :login OR email = :login";
                break;
        }
        
		$aRow = Linko::Database()->table('user')
            ->select('user_id, username, email, password')
            ->where($sCond)
            ->limit(1)
            ->query(array(':login' => $sLogin))->fetchRow();
		
		if(isset($aRow['user_id']))
		{
            $this->logout();

            if($bVerifyPassword)
            {
                if(Linko::Hasher()->compare($sPassword, $aRow['password']) === false)
                {
                    return array(false, Lang::t('user.no_user_found_with_such_password'));
                }
            }

            if(!Linko::Model('User')->isVerified($aRow['user_id']))
            {
                return array(
                    false,
                    Lang::t('user.account_pending_verification_and_cannot_login')
                );
            }
			
			$sData = sha1(mt_rand() . Linko::Request()->getIp() . Linko::Request()->getUserAgent());
			
            $sHash = Linko::Hasher()->hash($sData);
            
			$sHash = substr($sHash, (strpos($sHash, '.') + 1));
			
			Linko::Session()->set('user_id', $aRow['user_id']);
			Linko::Session()->set('user_hash', $sHash);
			
            // Update the login hash
			Linko::Database()->table('user')
                ->update(array('login_hash' => $sHash))
				->where("user_id = :id")
					->query(array(':id' => $aRow['user_id']));
			
			Linko::Plugin()->call('user.login', $aRow['user_id']);
			
			return array($aRow['user_id'], 'Log');
		}
		
		return array(false, Lang::t('user.invalid_login_credentials'));
	}
	
	public function logout()
	{
		Linko::Session()->remove('user_id');
		Linko::Session()->remove('user_hash');
		
		Linko::Plugin()->call('user.logout');
		
		return true;
	}
	
	private function _generateHash()
	{
		return md5(uniqid() . time());	
	}
	
	private function _setVisitor()
	{
		$this->_aUser = array(
			'user_id' => 0,
			'username' => 'guest',
			'password' => null,
			'email' => null,
			'role_id' => 3,
			'login_hash' => null
		);
	}
}

?>