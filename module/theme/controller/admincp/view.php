<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage theme : admincp\view.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Theme_Controller_Admincp_View extends Linko_Controller
{
    public function main()
    {
        $sTheme = $this->getParam('theme');
        $sType = $this->getParam('type');

        if($aSett = Input::post('sett'))
        {
            if(Linko::Model('Theme/Action')->saveSetting($sType, $sTheme, $aSett))
            {
                Linko::Flash()->success('Settings Saved!');
                Linko::Response()->redirect('self');
            }
        }

        Linko::Template()->setTitle('Theme', $sTheme)
            ->setBreadcrumb(array(), $sTheme)
            ->setScript('jquery/colorpicker.js', 'asset_js')
            ->setScript('admincp.js', 'module_theme')
            ->setStyle('jquery/colorpicker/colorpicker.css', 'asset_css')
            ->setVars(array(
                'sTheme' => $sTheme,
                'sType' => $sType,
                'aTheme' => Linko::Model('Theme')->getTheme($sType, $sTheme),
	            'aSettings' => Linko::Model('Theme')->getThemeSettings($sType, $sTheme)
        ));
    }
}

?>