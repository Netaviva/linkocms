<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage locale : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Locale_Plugin_Admincp
{
    public function init()
    {
        Linko::Model('Admincp')->addRoute('locale', array(
            'id' => 'locale:admincp',
            'controller' => 'locale/admincp/index'
        ));

        Linko::Model('Admincp')->addRoute('locale/translation(/[lang_id])', array(
            'id' => 'locale:admincp:translation',
            'controller' => 'locale/admincp/translation/index',
            'rules' => array('module' => ':alnum', 'locale' => ':alnum')
        ));
        
        Linko::Model('Admincp')->addRoute('locale/translation/[lang_id]/[module](/[page])', array(
            'id' => 'locale:admincp:translation:module',
            'controller' => 'locale/admincp/translation/module',
            'rules' => array('module' => ':alnum', 'page' => ':int', 'locale' => ':alnum')
        ));
                
        Linko::Model('Admincp')->addMenu('Localization', array(
            'Languages Manager' => Linko::Url()->make('locale:admincp'),
            'Translation Manager' => Linko::Url()->make('locale:admincp:translation')
        ));
    }
}

?>