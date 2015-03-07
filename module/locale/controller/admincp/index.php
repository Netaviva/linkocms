<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage locale : admincp\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Locale_Controller_Admincp_Index extends Linko_Controller
{
    public function main()
    {
		Linko::Template()->setTitle('Language Manager')
        ->setBreadcrumb(array(
			'Languages',
		), 'Languages')
        ->setVars(array(
            'aLanguages' => Linko::Model('Locale/Language')->getLanguages()
        ));
    }
}

?>