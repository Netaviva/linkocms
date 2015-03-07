<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage locale : model - date\date.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Locale_Model_Date extends Linko_Model
{
    /**
     * Gets the timezones that'll be displayed in settings page
     */
    public function getTimezones()
    {
        $aZones = Linko::Locale('date')->getTimezones();

        $aTimezones = array();

        foreach($aZones as $sTimezone)
        {
            $aTimezones[$sTimezone] = $sTimezone;
        }

        return $aTimezones;
    }

    public function getMonths()
    {
        $aMonthNames = array(
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        );

        $aMonths = array();

        foreach($aMonthNames as $iMonth => $sMonthName)
        {
            $aMonths[$iMonth] = Lang::t('date.' . strtolower($sMonthName));
        }

        return $aMonths;
    }
}

?>