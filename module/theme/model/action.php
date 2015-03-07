<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage theme : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Theme_Model_Action extends Linko_Model
{
    public function saveSetting($sType, $sTheme, $aSettings)
    {
        Linko::Database()->table('theme')
            ->update(array(
                'theme_setting' => serialize($aSettings)
            ))
            ->where('theme_folder', '=', $sTheme)
            ->where('theme_type', '=', $sType)
            ->query();

        return true;
    }
}