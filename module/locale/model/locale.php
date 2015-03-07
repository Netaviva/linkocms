<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage locale : model - locale.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Locale_Model_Locale extends Linko_Model
{
	public function init()
	{
		foreach(Linko::Model('Locale/Language')->getLanguages() as $sLocale => $aLocale)
		{
			Linko::Locale()->addLocale($aLocale['locale_id'], $aLocale);

			Linko::Language()->addTranslation($aLocale['locale_id'], Linko::Model('Locale/Language')->getTranslations($aLocale['locale_id']));

			Linko::Language()->addRules($aLocale['locale_id'], Linko::Model('Locale/Language')->getRules($aLocale['locale_id']));
		}

		$sLocale = Linko::Model('Locale/Language')->getLanguageId();

		Linko::Locale()->setLocale($sLocale);

        if(!($sTimezone = Linko::Model('User/Auth')->getUserBy('time_zone')))
        {
            $sTimezone = Linko::Module()->getSetting('locale.default_timezone');
        }

        if(empty($sTimezone))
        {
            $sTimezone = date_default_timezone_get();
        }

        Linko::Template()->setTranslation(array('date.just_now', 'date.x_seconds_ago', 'date.a_minute_ago', 'date.x_minutes_ago', 'date.an_hour_ago', 'date.x_hours_ago', 'date.yesterday', 'date.x_days_ago'));

        Date::setTimezone($sTimezone);
	}
}

?>