<?php

class Setting_Model_Task extends Linko_Model
{
	public function module_install()
	{
		// Settings not tied to any module
		$aSettings = array(
			array('var' => 'cookie.prefix', 'title' => 'Cookie Prefix', 'type' => 'string', 'value' => 'linko', 'description' => 'This is the prefix that will be added to cookies and PHP sessions being set by the script.'),

			array('var' => 'cookie.path', 'title' => 'Cookie Path', 'type' => 'string', 'value' => '/', 'description' => 'The path to which the cookie is saved. Note: Path should always end in a forward slash. eg /folder/ or /my/sub/path/. Note: Setting invalid value may disable login.'),

			array('var' => 'cookie.domain', 'title' => 'Cookie Domain', 'type' => 'string', 'value' => '', 'description' => 'This sets the domain on which the cookie is activated. If you use multiple urls to access this site for example cms.site.com and www.site.com, you may want to change this so that logged in users will stay logged in when they visit both urls. Note: Setting invalid value may disable login.'),

            array('var' => 'date.format', 'title' => 'Date Format', 'type' => 'string', 'value' => 'F j, Y g:i a', 'description' => 'Date Time Format used to format how the time is displayed. Note: Not all modules may use this setting.')
		);

		// Create Categories
		$iCookieId = Linko::Model('Setting/Action')->addCategory('Cookies');
		$iDateId = Linko::Model('Setting/Action')->addCategory('Time/Date');

		foreach($aSettings as $aSetting)
		{
			if($iId = Linko::Model('Setting/Action')->add(null, $aSetting))
			{
				if(in_array($aSetting['var'], array('cookie.prefix', 'cookie.path', 'cookie.domain')))
				{
					Linko::Model('Setting/Action')->setSettingCategory($iCookieId, $iId);
				}

				if(in_array($aSetting['var'], array('date.format')))
				{
					Linko::Model('Setting/Action')->setSettingCategory($iDateId, $iId);
				}
			}
		}
	}
}