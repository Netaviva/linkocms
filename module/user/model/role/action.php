<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : model - role\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class User_Model_Role_Action extends Linko_Model
{
	/**
	 * Adds a new user role
	 *
	 * $iRoleId = Linko::Model('User/Role/Action')->addRole(array(
	 *		'title' => 'Banned'
	 * ));
	 *
	 * @param array $aVals
	 */
	public function addRole($aVals)
	{

	}

	/**
	 * Adds a new user role setting
	 *
	 * $iSetting = Linko::Model('User/Role/Action')->addSetting('user', array(
	 *      'user.can_ban_user' => 1,
	 *      'user.can_unban_user' => 0,
	 *      'user.can_edit_other_profile' => 1
	 * )
	 *
	 * @param string $sModule module id
	 * @param array $aVals key/value pair of setting_var and
	 *
	 * @return void
	 */
	public function addSetting($sModule, array $aVals)
	{
		$aSettings = array();

		$iCnt = 0;

		foreach($aVals as $sVar => $sValue)
		{
			$aSettings[$iCnt]['module_id'] = $sModule;
			$aSettings[$iCnt]['setting_var'] = $sVar;
			$aSettings[$iCnt]['setting_value'] = $sValue;

			$iCnt++;
		}

		Linko::Database()->table('user_role_setting')
			->insert($aSettings)
			->query();

		return true;
	}

	/**
	 * Sets the setting of a role
	 *
	 * Linko::Model('User/Role/Action')->setSetting(3, 'user.can_suspend_user', false)
	 *
	 * or for multiple settings
	 *
	 * Linko::Model('User/Role/Action')->setSettng(3, array(
	 *      'user.can_suspend_user' => false,
	 *      'user.can_edit_other_account' => false
	 * ));
	 *
	 * @param int $iRoleId
	 * @param array|string $mVar
	 * @param mixed $mValue
	 *
	 * @return bool
	 */
	public function setSetting($iRoleId, $mVar, $mValue = null)
	{
		if(!is_array($mVar))
		{
			$mVar = array($mVar => $mValue);
		}

		$aInsert = array();
		foreach($mVar as $sVar => $mValue)
		{
			$aInsert[] = array($iRoleId, $sVar, $mValue);
		}

		// Delete all setting vars in this role
		Linko::Database()->table('user_role_setting_data')
			->delete()
			->where('role_id', '=', $iRoleId)
			->whereIn('setting_var', array_keys($mVar))
			->query();

		// insert the setting vars
		Linko::Database()->table('user_role_setting_data')
			->insert(array('role_id', 'setting_var', 'setting_value'), $aInsert)
			->query();

		Linko::Plugin()->call('user.set_role_setting', $iRoleId, $mVar);

		return true;
	}

	/**
	 * @param $sVar
	 */
	public function deleteSetting($sVar)
	{
		Linko::Database()->table('');
	}

	public function deleteModuleSetting($sModule)
	{

	}
}

?>