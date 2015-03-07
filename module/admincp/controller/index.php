<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage admincp : index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Admincp_Controller_Index extends Linko_Controller
{
	public function main()
	{
        Linko::Router()
			->setBase('admincp')
			->setKey('admincp');

        Linko::Template()
			->setType('backend')
            ->setStyle(array(
                'jquery/ui-darkness/jquery-ui.css'
            ), 'asset_css')
            ->setScript(array(
                'jquery/jquery-ui-latest.js'
            ), 'asset_js', 'header')
            ->setScript('admincp.js', 'module_admincp');
        
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            if(($aVals = Input::post('val')) && (Arr::hasKeys($aVals, 'email', 'password')))
            {
                if(Linko::Model('User/Auth')->login($aVals['email'], $aVals['password'], false, 'email'))
                {
                    Linko::Response()->redirect('self');
                }
            }
            Linko::Url()->make();
            return Linko::Module()->set('admincp/login');
        }
        
		Linko::Template()
			->setTitle('AdminCP')
			->setBreadcrumb(array(
					'Home' => Linko::Url()->make('admincp')
				)
			);
		
		$sUri = $this->getParam('uri');
		
		Linko::Model('Admincp')->init();
		
		Linko::Model('Admincp')->setController($sUri);
	}
}

?>