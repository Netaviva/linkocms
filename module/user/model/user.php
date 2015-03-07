<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : model - user.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class User_Model_User extends Linko_Model
{
    public function isUsernameAllowed($sUsername)
    {
        $aDissallowed = array_merge(
            array_keys(Linko::Module()->getModules()),
            array('setting', 'profile', 'user', 'admin', 'administrator', 'admincp', 'linko', 'linkocms', 'reminder')
        );

        if(in_array($sUsername, $aDissallowed))
        {
            return false;
        }

        return true;
    }

    /**
     * User_Model_User::getUser()
     * Can also retrieve values from other tables
     * role => adds user_role table
     * language => adds language table
     *
     * @param mixed $mUser user_id or username
     * @param array $aData extra data to get
     * @return array
     */
	public function getUser($mUser, $aData = array())
	{
	    $sCond = is_numeric($mUser) ? "u.user_id = :id" : "u.username = :id";

        $oUser = Linko::Database()->table('user', 'u')
            ->select($this->getTableFields('u'), 'ud.*')
            ->leftJoin('user_data', 'ud', 'ud.user_id = u.user_id');

        if(in_array('role', $aData))
        {
            $oUser->select('ur.role_title')
                ->leftJoin('user_role', 'ur', 'ur.role_id = u.role_id');
        }

        if(in_array('language', $aData))
        {
            $oUser->select('l.title AS language_title')
                ->leftJoin('language', 'l', 'l.locale_id = u.locale_id');
        }

        if(in_array('country', $aData))
        {
            $oUser->select('c.country_title')
                ->leftJoin('country', 'c', 'c.country_id = u.country_id');
        }

        if(in_array('password', $aData))
        {
            $oUser->select('u.password');
        }

        $aUser = $oUser->where($sCond)->query(array(':id' => $mUser))->fetchRow();

        /**if($aUser['activated'] < 1)
        {
            $aUser['role_id'] = CMS::USER_ROLE_GUEST;
        }/**/

        return $aUser;
	}

    public function isVerified($iUser)
    {
        $iActivated = Linko::Database()->table('user')
            ->select('activated')
            ->where('user_id', '=', $iUser)
            ->where('activated', '!=', 0)
            ->query()
            ->fetchValue();

        return (bool)$iActivated;
    }

    public function getTableFields($sAlias = null, $sDataAlias = null)
    {
        $aDataField = array();

        $aField = array(
            'user_id',
            'username',
            'email',
            'user_photo',
	        'role_id',
            'locale_id',
            'country_id',
            'time_zone',
            'time_dst_check',
            'time_joined',
            'activated',
            'gender'
        );

        if($sDataAlias)
        {
            $aDataField = array(
                'firstname',
                'lastname',
            );
        }
        
        $sFields = '';
        
        foreach($aField as $sField)
        {
            $sFields .= ($sAlias ? $sAlias . '.' : '') . $sField . ', ';
        }

        foreach($aDataField as $sField)
        {
            $sFields .= ($sDataAlias ? $sDataAlias . '.' : '') . $sField . ', ';
        }
    
        $sFields = rtrim($sFields, ', ');
        
        return $sFields;
    }

    public function getFullname($aUser)
    {
        $sLastname = isset($aUser['lastname']) ? $aUser['lastname'] : null;
        $sFirstname = isset($aUser['firstname']) ? $aUser['firstname'] : null;
        $sUsername = isset($aUser['username']) ? $aUser['username'] : null;

        if($sLastname == null && $sFirstname == null)
        {
            return $sUsername;
        }

        return ucwords($sLastname . ' ' . $sFirstname);
    }

    public function getGender($sRaw)
    {
        $sRaw = strtoupper($sRaw);

        $aLang = array(
            'M' => Lang::t('user.male'),
            'F' => Lang::t('user.female'),
            'N' => Lang::t('user.none')
        );

        return array_key_exists($sRaw, $aLang) ? $aLang[$sRaw] : $aLang['N'];
    }

    public function getTotalUsers()
    {
        return Linko::Database()->table('user')
            ->select()
            ->query()
            ->getCount();
    }
}

?>