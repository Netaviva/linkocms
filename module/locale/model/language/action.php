<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage locale : model - language\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Locale_Model_Language_Action extends Linko_Model
{
    public function deleteLanguage($sLocale)
    {
        // delete all translations related to this locale
        // delete the locale
        // clear cache
    }

    public function addTranslation($sVar, $sValue, $sModule, $sLangId = 'en_GB')
    {
        /**$bExists = Linko::Database()->table('language_translation')
            ->select('translation_id')
            ->where('translation_var', '=', $sVar)
            ->where('locale_id', '=', $sLocale)
            ->limit(1)
            ->query()->getCount();

        if($bExists)
        {
            return 0;
        }/**/

        $iId = Linko::Database()->table('language_translation')
            ->insert(array(
            'locale_id' => $sLangId,
            'module_id' => $sModule,
            'translation_var' => $sVar,
            'translation_value' => $sValue,
        ))
            ->query()
            ->getInsertId();

        Linko::Plugin()->call('locale.add_translation');

        return $iId;
    }

    public function updateTranslation($sVar, $sValue, $sModule, $sLangId = 'en_GB', $bInsertIfNotExists = false)
    {
        if(!$sLangId)
        {
            $sLangId = 'en_GB';
        }

        $bExists = Linko::Database()->table('language_translation')
            ->select('translation_id')
            ->where('translation_var', '=', $sVar)
            ->where('locale_id', '=', $sLangId)
            ->where('module_id', '=', $sModule)
            ->limit(1)
            ->query()->getCount();

        if(!$bExists)
        {
            if($bInsertIfNotExists)
            {
                return $this->addTranslation($sVar, $sValue, $sModule, $sLangId);
            }

            return false;
        }

        $iId = Linko::Database()->table('language_translation')
            ->update(array(
                'translation_value' => $sValue,
            ))
            ->where('translation_var', '=', $sVar)
            ->where('locale_id', '=', $sLangId)
            ->query();

        Linko::Cache()->delete(array('application', 'translation_' . $sLangId));

        Linko::Plugin()->call('locale.update_translation', $iId);

        return true;
    }

    public function deleteTranslation($sVar, $sLocale = 'en_GB')
    {
        $iId = Linko::Database()->table('language_translation')
            ->delete()
            ->where('translation_var', '=', $sVar)
            ->where('locale_id', '=', $sLocale)
            ->query();

        return true;
    }

    public function deleteModuleTranslations($sModule)
    {
        $iId = Linko::Database()->table('language_translation')
            ->delete()
            ->where('module_id', '=', $sModule)
            ->query();

        return true;
    }
}

?>