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
 * Register Template Plugin: Pager
 * Display Pagination Links
 * 
 * use:
 * In Template/Layout files
 * 
 * $this->plugin('pager');
 * 
 */
class Template_Plugin_Pager
{
    public function start()
    {
        return Linko::Template()->getLayout('pager', array(
            'aPager' => Linko::Pager()->get()
        ));
    }
}

?>