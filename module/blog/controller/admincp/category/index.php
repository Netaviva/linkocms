<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage blog : admincp\category\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Blog_Controller_Admincp_Category_Index extends Linko_Controller
{
    public function main()
    {
        $aCategories = Linko::Model('Blog')->getCategories();
        
 		Linko::Template()->setBreadcrumb(array(
			'Blog' => Linko::Url()->make('blog:admincp'),
            'Categories'
		), 'Manage Categories')
		->setVars(array(
            'aCategories' => $aCategories
		))
        ->setTitle('Blog', 'Manage Categories');
    }
}

?>