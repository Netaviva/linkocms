<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : model - task.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class User_Model_Task extends Linko_Model
{
    public function activity_format($aParams)
    {

    }

	/**
	 * This method is called during installation of this module.
	 */
	public function module_install()
	{
		// insert pre defined user roles
		Linko::Database()->table('user_role')
			->insert(array('role_id', 'role_title', 'system'),
			array(
				array(CMS::USER_ROLE_ADMIN, 'Administrator', true),
				array(CMS::USER_ROLE_USER, 'Registered', true),
				array(CMS::USER_ROLE_GUEST, 'Guest', true)
			))
			->query();

		// add user role settings
		Linko::Model('User/Role/Action')->addSetting('user', array(
			'user.can_delete_own_account' => 1,
			'user.can_account_deleted_by_other' => 1,
			'user.can_edit_own_account' => 1,
			'user.can_edit_other_account' => 0
		));

		// set setting for Administrators user role
		Linko::Model('User/Role/Action')->setSetting(CMS::USER_ROLE_ADMIN, array(
			'user.can_delete_own_account' => 0,
			'user.can_account_deleted_by_other' => 0,
			'user.can_edit_own_account' => 1,
			'user.can_edit_other_account' => 1
		));

		// set setting for Registered User Role
		Linko::Model('User/Role/Action')->setSetting(CMS::USER_ROLE_USER, array(
			'user.can_delete_own_account' => 1,
			'user.can_account_deleted_by_other' => 1,
			'user.can_edit_own_account' => 1,
			'user.can_edit_other_account' => 0
		));

		// set setting for Guest User Role
		Linko::Model('User/Role/Action')->setSetting(CMS::USER_ROLE_GUEST, array(
			'user.can_delete_own_account' => 0,
			'user.can_account_deleted_by_other' => 0,
			'user.can_edit_own_account' => 0,
			'user.can_edit_other_account' => 0
		));

		// Add Mails Vars
		Linko::Model('Mail/Action')->add(array(
			'var' => 'user.account_verification',
			'subject' => '{site_title} - Account Activation',
			'body' => "To verify your account to continue using our site,
follow the link below and complete the steps after:

<a href=\"{verification_link}\">Click here to verify your account.</a>

If you cannot click on the link, copy the url below

{verification_link}

<a href=\"{site_url}\">{site_title}</a>
{signature}",
			'title' => 'User Account Verification',
			'description' => 'mail sent to user to click a link to verify their account',
			'param' => array(
				'verification_link' => 'holds the link that redirects the user to the verification page'
			)
		));

		Linko::Model('Mail/Action')->add(array(
			'var' => 'user.welcome',
			'subject' => '{site_title} - Welcome',
			'body' => "Hello {username}
Thank you for joining our network. We have a lot for you. Follow the link below to explore our site.
{site_url}
{signature}",
			'title' => 'User Welcome',
			'description' => 'mail sent to user after completing full registeration',
			'param' => array(
				'username' => 'Username of the registered user'
			)
		));

		Linko::Model('Mail/Action')->add(array(
			'var' => 'user.password_reset_request',
			'subject' => '{site_title} -  Reset Password',
			'body' => "
You have requested to reset your password from {site_title}. If you did not request this, just ignore it.
Note. This link will be invalid {expire_time} hours after the request was made.
To reset your password, click the link below:

<a href=\"{reset_link}\">Set a new Password</a>

If you do not see the link above, copy the link below and paste it on your browser address bar.

{reset_link}",
			'title' => 'Password Reset Request',
			'description' => 'Sent to the user after making a password reset request.',
			'param' => array(
				'expire_time' => 'time that reset key will expire',
				'reset_link' => 'Holds the link that\'ll redirect users to the page where they\'ll reset their password'
			)
		));
	}

    public function module_upgrade($sVersion)
    {
        // if we are coming from 1.0.0
        if(version_compare($sVersion, '1.0.0', '<='))
        {
            // the birthday field wasnt available.
            Linko::Database()->table('user')
                ->addColumn('birthday', array(
                    'type' => 'int(10)',
                    'unsigned' => true,
                    'default' => 0
                ))
                ->query();
        }
    }

	/**
	 * This module is a required module.
	 */
	public function module_uninstall()
	{

	}
}

?>