<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage blog : block - category.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Blog_Block_Category extends Linko_Controller
{
    public function main()
    {
        Linko::Template()->setVars(array(
            'aCategories' => Linko::Model('Blog')->getCategories(true)
        ));
    }
}

?>