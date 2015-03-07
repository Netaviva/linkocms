<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage admincp : system\phpconf.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Admincp_Controller_System_Phpconf extends Linko_Controller
{
	public function main()
	{
		$aPhp = array(
			'PHP Version' => PHP_VERSION,
			'Magic Quotes Runtime' => ((bool) ini_get('magic_quotes_runtime')) == true ? 'Enabled' : 'Disabled',
			'Maximum Execution Time' => ini_get('max_execution_time'),
			'Memory Limit' => ini_get('memory_limit') . ' (' . File::size($this->_getUsableMemory()) . ' Usable)',
			'Maximum Post Size' => ini_get('post_max_size'),
			'GZCompress' => ((bool) function_exists('gzcompress')) ? 'Enabled' : 'Disabled',
			'Output Buffering' => ((bool) ini_get('output_buffering')) == true ? 'Enabled' : 'Disabled',
			'Safe Mode' => ((bool) ini_get('safe_mode')) == true ? 'Enabled' : 'Disabled',
			'File Uploads' => ((bool) ini_get('file_uploads')) == true ? 'Enabled' : 'Disabled',
			'Upload Maximum Size' => ini_get('upload_max_filesize'),
			'Register Globals' => ((bool) ini_get('register_globals')) == true ? 'Enabled' : 'Disabled',
		);
		
		$aExtension = array(
		  'GD Library' => 'Yes (version 2)'
		);
		
		Linko::Template()
			->setTitle('System Information', 'PHP')
			->setBreadcrumb(array(
				'System Information',
				'PHP',
			), 'PHP Information');
		
		Linko::Template()->setVars(array(
				'aPhp' => $aPhp,
				'aExtension' => $aExtension,
			)
		);		
	}
	
	private function _getUsableMemory()
	{
		$sVal = trim(@ini_get('memory_limit'));
	
		if (preg_match('/(\\d+)([mkg]?)/i', $sVal, $aRegs))
		{
			$sMemoryLimit = (int) $aRegs[1];
			switch ($aRegs[2])
			{	
				case 'k':
				case 'K':
					$sMemoryLimit *= 1024;
					break;	
				case 'm':
				case 'M':
					$sMemoryLimit *= 1048576;
					break;	
				case 'g':
				case 'G':
					$sMemoryLimit *= 1073741824;
					break;
			}
			
			$sMemoryLimit /= 2;
		}
		else
		{
			$sMemoryLimit = 1048576;
		}
	
		return $sMemoryLimit;
	}
}

?>