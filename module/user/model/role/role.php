<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : model - role\role.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class User_Model_Role extends Linko_Model
{
	/**
	 * @var array User Role Settings
	 */
	private $_aSetting = array();

	public function __construct()
	{

	}

	/**
	 * Gets the current user role id
	 *
	 * @return int
	 */
	public function getUserRoleId()
	{
		return Linko::Model('User/Auth')->getUserBy('role_id');
	}

	/**
	 * Gets all user roles
	 *
	 * @return array
	 */
	public function getRoles()
	{
		Linko::Cache()->set(array('user', 'user_roles'));

		if(!$aRoles = Linko::Cache()->read())
		{
			$aRoles = Linko::Database()->table('user_role')
	            ->select()
				->query()
				->fetchRows();

			Linko::Cache()->write($aRoles);
		}

		return $aRoles;
	}

	/**
	 * Gets setting for a user role
	 *
	 * @param string $sVar setting reference key
	 * @param int $iRole user role id. If 0 is passed, uses the current user role id
	 *
	 * @return mixed setting value
	 */
	public function getSetting($sVar, $iRole = 0)
	{
		$iRole = $iRole == false ? $this->getUserRoleId() : $iRole;

		if(!array_key_exists($iRole, $this->_aSetting))
		{
			$this->_buildSetting($iRole);
		}

		return isset($this->_aSetting[$iRole][$sVar]) ? $this->_aSetting[$iRole][$sVar] : null;
	}

	private  function _buildSetting($iRole)
	{
		Linko::Cache()->set(array('user', 'role_setting_' . $iRole));

		$this->_aSetting[$iRole] = array();

		if(Linko::Cache()->isCached())
		{
			$this->_aSetting[$iRole] = Linko::Cache()->read();
		}
		else
		{
			$aRows = Linko::Database()->table('user_role_setting', 'urs')
				->select('urs.*', 'ursd.*', array('urs.setting_value', 'default_value'))
				->leftJoin('user_role_setting_data', 'ursd', 'ursd.setting_var = urs.setting_var AND ursd.role_id = :role_id')
				->group('urs.setting_id')
				->query(array(':role_id' => $iRole))
				->fetchRows();

			foreach($aRows as $aRow)
			{
				if(is_null($aRow['setting_value']))
				{
					$aRow['setting_value'] = $aRow['default_value'];
				}

				$this->_aSetting[$iRole][$aRow['setting_var']] = (bool)$aRow['setting_value'];
			}

			Linko::Cache()->write($this->_aSetting[$iRole]);
		}
	}
}

?>