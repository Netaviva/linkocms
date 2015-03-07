<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage locale : model - country\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Locale_Model_Country_Action extends Linko_Model
{
    public function addCountry($aVals)
    {
        if(!Arr::hasKeys($aVals, 'country_id', 'title'))
        {
            return false;
        }


    }

    public function addCountries(Array $aVals)
    {
        $aInsert = array();

        foreach($aVals as $sCode => $sCountry)
        {
            $aInsert[] = array($sCode, $sCountry);
        }

        Linko::Database()->table('country')
            ->insert(array('country_id', 'country_title'), $aInsert)
            ->query();

        return true;
    }
}

?>