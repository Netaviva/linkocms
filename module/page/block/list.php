<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage page : block - list.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Page_Block_List extends Linko_Controller
{
	public function main()
	{
		$aPages = array_filter(Linko::Model('Page')->getPages(), function($aPage){
            return $aPage['page_type'] != 'module';
        });

        Linko::Template()->setVars(array(
            'aPages' => $aPages
        ));
	}

}

?>