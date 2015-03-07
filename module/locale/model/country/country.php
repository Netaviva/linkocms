<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage locale : model - country\country.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Locale_Model_Country extends Linko_Model
{
    public function getCountries()
    {
        Linko::Cache()->set(array('locale', 'countries'));

        if(!$aCountries = Linko::Cache()->read())
        {
            $aCountries = Linko::Database()->table('country')
                ->select()
                ->order('ordering ASC, country_title ASC')
                ->query()
                ->fetchRows();

            Linko::Cache()->write($aCountries);
        }

        return $aCountries;
    }

    public function getCountryById($sCountryId)
    {

    }
}

?>