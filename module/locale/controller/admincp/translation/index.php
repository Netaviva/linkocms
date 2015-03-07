<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage locale : admincp\translation\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Locale_Controller_Admincp_Translation_Index extends Linko_Controller
{
    public function main()
    {
        $iPage = $this->getParam('page');
        
        $sModule = $this->getParam('module');
        
        $sLangId = $this->getParam('lang_id');
        
        $aModules = Linko::Model('Locale/Language')->getTranslatedModules($sLangId);
        
        $aLanguages = Linko::Model('Locale/Language')->getLanguages();
        
		Linko::Template()->setTitle('Manage Translations')
        ->setBreadcrumb(array(
			'Translations' => null,
		), 'Manage Translations');
        
        if($sLangId)
        {
            Linko::Template()->setTitle($sLangId)
                ->setBreadcrumb(array(
        			'Translations' => Linko::Url()->make('locale:admincp:translation'),
                    $sLangId => null,
        		), 'Manage Translations');
        }
        
        Linko::Template()
        ->setVars(array(
            'aModules' => $aModules,
            'aLanguages' => $aLanguages,
            'sLangId' => $sLangId,
        ));
    }
}

?>