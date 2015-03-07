<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage locale : model - language\language.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Locale_Model_Language extends Linko_Model
{
    /**
     * Gets current locale
     */
    public function getLanguageId()
    {
        // if is user, get the user locale
        if(Linko::Model('User/Auth')->isUser())
        {
            $sLocale = Linko::Model('User/Auth')->getUserBy('locale_id');
        }
        else
        {
            // for guest, try to get the locale id from session
            $sLocale = Linko::Session()->get('user_locale_id');
        }

        // if locale is empty, use the default
        if($sLocale == '')
        {
            $sLocale = 'en_GB';
        }

        return $sLocale;
    }

    public function getLanguages()
    {
        Linko::Cache()->set(array('locale', 'locales'));

        if(!$aLocales = Linko::Cache()->read())
        {
            $aRows = Linko::Database()->table('language')
                ->select()
                ->query()
                ->fetchRows();

            $aLocales = array();

            foreach($aRows as $aRow)
            {
                $aLocales[$aRow['locale_id']] = $aRow;
            }

            Linko::Cache()->write($aLocales);
        }

        return $aLocales;
    }

    public function getTranslatedModules($sLangId)
    {
        $aRows = Linko::Database()->table('language_translation', 't')
            ->select('t.module_id', 'COUNT("module_id") AS total_translation')
            ->where('locale_id', '=', $sLangId)
            ->group('t.module_id')
            ->query()
            ->fetchRows();

        return $aRows;
    }

    /**
     * @param string $sLangId language locale id
     * @param string $sModule module id
     * @param int $iPage current page
     * @param int $iLimit limit
     * @return array
     */
    public function getModuleTranslations($sLangId, $sModule, $iPage = 0, $iLimit = 0)
    {
        // get lang translations
        list($iTotal, $aRows) = Linko::Database()->table('language_translation')
            ->select()
            ->where('module_id', '=', $sModule)
            ->where('locale_id', '=', $sLangId)
            ->query()
            ->paginate($iPage, $iLimit);

        // get def translations
        list($iDefTotal, $aDefRows) = Linko::Database()->table('language_translation')
            ->select()
            ->where('module_id', '=', $sModule)
            ->where('locale_id', '=', 'en_GB')
            ->query()
            ->paginate($iPage, $iLimit);

        $aValues = array();
        $aTranslations = array();

        // build array of lang values
        foreach($aRows as $aRow)
        {
            $aValues[$aRow['translation_var']] = $aRow['translation_value'];
        }

        // rebuild translation (if translation exists for the lang id, use it else use the default)
        foreach($aDefRows as $iKey => $aRow)
        {
            $aTranslations[$iKey] = $aRow;
            $aTranslations[$iKey]['translation_value'] = isset($aValues[$aRow['translation_var']]) ? $aValues[$aRow['translation_var']] : $aRow['translation_value'];
        }

        return array($iTotal, $aTranslations);
    }

	public function getTranslations($sLocale)
	{
		Linko::Cache()->set(array('application', 'translation_' . $sLocale));

		if(!$aTranslations = Linko::Cache()->read())
		{
			$aTranslations = array();

			// Load module translations
			foreach(Linko::Module()->getModules() as $aModule)
			{
				$sFile = $aModule['dir'] . 'language' . DS . $sLocale . '.php';

				if(File::exists($sFile))
				{
					$aTranslations = array_merge($aTranslations, Arr::get(require($sFile), 'translation', array()));
				}
			}

			// Load translations from database
			$aRows = Linko::Database()->table('language_translation')
				->select()
				->where("locale_id = :locale")
				->query(array(':locale' => $sLocale))
				->fetchRows();

			foreach($aRows as $aRow)
			{
				$aTranslations[$aRow['translation_var']] = $aRow['translation_value'];
			}

			Linko::Cache()->write($aTranslations);
		}

		//Arr::dump($aTranslations);

		return $aTranslations;
	}

	public function getRules($sLocale)
	{
		Linko::Cache()->set(array('application', 'translation_rule_' . $sLocale));

		if(!$aRules = Linko::Cache()->read())
		{
			$aRules = array();

			// Load module translations
			foreach(Linko::Module()->getModules() as $aModule)
			{
				$sFile = $aModule['dir'] . 'language' . DS . $sLocale . '.php';

				if(File::exists($sFile))
				{
					$aRules = array_merge($aRules, Arr::get(require($sFile), 'rule', array()));
				}
			}

			$aRows = Linko::Database()->table('language_translation_rule')
				->select()
				->where("locale_id = :locale")
				->query(array(':locale' => $sLocale))
				->fetchRows();

			foreach($aRows as $aRow)
			{
				$aRules[$aRow['translation_var']][] = $aRow['translation_value'];
			}

			Linko::Cache()->write($aRules);
		}

		//Arr::dump($aRules);

		return $aRules;
	}
}

?>