<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage contentblock : block - view.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Contentblock_Block_View extends Linko_Controller
{
	public function main()
	{
		$iId = $this->getParam('id');
		$aBlock = Linko::Model('Contentblock')->get($iId);
		
		Linko::Template()->setVars(array(
			'contentblock_id' => $iId,
			'aBlock' => $aBlock
		));
	}
}