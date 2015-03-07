<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage locale : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Locale_Model_Action extends Linko_Model
{
	public function addLocale($aVals)
	{
		if(!array_key_exists('locale_id', $aVals))
		{
			return Linko::Error()->set(Lang::t('locale.locale_id_required'));
		}

		if(!array_key_exists('title', $aVals))
		{
			return Linko::Error()->set(Lang::t('locale.locale_title_required'));
		}

		$aVals = array_merge(array(
			'charset' => 'utf-8',
			'direction' => 'ltr'
		), $aVals);

		$sInsertId = Linko::Database()->table('language')
			->insert($aVals)
			->query()
			->getInsertId();

		Linko::Plugin()->call('locale.add_locale', $sInsertId, $aVals);
	}
}

?>