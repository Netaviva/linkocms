<?php

class Mail_Plugin_Admincp
{
	public function init()
	{
		Linko::Model('Admincp')->addRoute('setting/mail', array(
			'id' => 'mail:setting',
			'controller' => 'mail/admincp/index'
		));

		Linko::Model('Admincp')->addMenu('Settings', array(
			'Manage Mails' => Linko::Url()->make('mail:setting')
		));
	}
}