<?php

/**
 * Core File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

defined('LINKO') or exit();

/** 
 * Extending Classes
 */

function helpers_html_required()
{
	return '<span class="helper-required"> * </span>' ;
}

/**
 *	Html::required()
 *	This will display an asterix used in 
 *	forms to highlight that a field is required.
 */
Html::extend('required', 'helpers_html_required');

/**
 * @method Linko::Pclzip
 */
Linko::extend('Pclzip', function($sArchive)
{
	require_once APP_PATH . 'include' . DS . 'library' . DS . 'pclzip' . DS . 'pclzip.lib.php';

	return new PclZip($sArchive);
});

/**
 * @method Linko::Log
 */
Linko::extend('Log', function($sWriter = 'file')
{
	$oWriter = null;

	switch($sWriter)
	{
		case 'file':
			$oWriter = new Linko_Log_Writer_File();
			$oWriter->setLogPath(DIR_LOG);
			break;
	}

	$oLogger = new Linko_Log();

	$oLogger->setWriter($oWriter);

	return $oLogger;
});