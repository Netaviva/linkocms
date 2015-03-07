<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage locale : admincp\translation\module.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Locale_Controller_Admincp_Translation_Module extends Linko_Controller
{
    public function main()
    {
        $oTpl = Linko::Template();
        
        $iPage = $this->getParam('page');
        
        $sModule = $this->getParam('module');
        
        $sLangId = $this->getParam('lang_id');
        
        $sAction = null;
        
        $iId = null;
        
        list($iTotal, $aTranslations) = Linko::Model('Locale/Language')->getModuleTranslations($sLangId, $sModule, $iPage, 10);

        Linko::Pager()->set(array(
            'current_page' => $iPage,
            'rows_per_page' => 10,
            'route_id' => Linko::Router()->getId(),
            'route_key' => 'page',
            'route_param' => array('module' => $sModule, 'lang_id' => $sLangId),
            'total_items' => $iTotal
        ));
                
        if($aAct = Input::post('act'))
        {
            $aVars = (array)Input::post('var');
            $aVals = Input::post('val');

            switch(key($aAct))
            {
                case 'save_all':

                    foreach($aVals as $sVar => $sValue)
                    {
                        Linko::Model('Locale/Language/Action')->updateTranslation($sVar, $sValue, $sModule, $sLangId, true);
                    }

                    Linko::Flash()->success('Translations Updated.');
                    Linko::Response()->redirect('self');                                       
                    break;
                case 'save_selected':
                    foreach($aVals as $sVar => $sValue)
                    {
                        if(!in_array($sVar, $aVars))
                        {
                            continue;
                        }
                        
                        Linko::Model('Locale/Language/Action')->updateTranslation($sVar, $sValue, $sLangId);
                    }
                    
                    Linko::Flash()->success('Translations Updated.');
                    Linko::Response()->redirect('self');
                    break;
                case 'delete_selected':

                    foreach($aVals as $sVar => $sValue)
                    {
                        if(!in_array($sVar, $aVars))
                        {
                            continue;
                        }
                        
                        Linko::Model('Locale/Language/Action')->deleteTranslation($sVar, $sLangId);
                    }
                    
                    Linko::Flash()->success('Selected Translations  Deleted.');
                    Linko::Response()->redirect('self');
                                   
                    break;
            }
        }
        
        Linko::Template()->setTitle('Manage Translations')
        ->setBreadcrumb(array(
			'Translations' => Linko::Url()->make('locale:admincp:translation'),
            $sLangId => Linko::Url()->make('locale:admincp:translation', array('lang_id' => $sLangId)),
            $sModule
		), 'Manage Translations')
        ->setVars(array(
            'sModule' => $sModule,
            'iTotal' => $iTotal,
            'aTranslations' => $aTranslations,
        ));
    }
}

?>