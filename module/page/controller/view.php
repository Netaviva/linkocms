<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage page : view.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Page_Controller_View extends Linko_Controller
{
    public function main()
	{
        $oUserAuth = Linko::Model('User/Auth');

        $sSlug = $this->getParam('slug');

        $bIsHome = $sSlug == '' ? true : false;

        Linko::Router()->setKey('page');

	    // Set Routes
        Linko::Model('Page')->setRoutes();

        // reroute the slug
		$oRoute = Linko::Router()->route($this->getParam('slug'));

        $bSkipCache = false;
        
        if($bIsHome)
        {
            $sSlug = Linko::Model('Page')->getHomepage($sSlug);
        }
        
        // If the route exists, use the raw route regex as the page slug
        if($oRoute->id)
        {
            $sSlug = ltrim($oRoute->route->getRawRegex(), '/');
            
            // Only user defined pages are cached. if the page is a dynamic page, we skip caching that page db query
            $bSkipCache = true;
        }
        		
        $aPage = Linko::Model('Page')->getPage($sSlug, true, $bSkipCache);

		$sRouteId = Linko::Router()->getId();
        
        // Return error if page is not found
		if(!isset($aPage['page_id']))
		{
			return Linko::Module()->set('_404_');
		}
		
        // Return error if user doesnt have access to view this page
		if(in_array($oUserAuth->getUserBy('role_id'), $aPage['dissallow_access']))
		{
			return Linko::Module()->set('page/denied', array(
                'message' => 'You do not have the necessary permission to view this page.'
            ));
		}

		// page inactive
        if($aPage['page_status'] == 0)
        {
            return Linko::Module()->set('_404_');
        }

        $bModulePage = $aPage['page_type'] == 'module' ? true : false;
        
        // Get the module from the controller
        $sModule = substr($aPage['component_file'], 0, strpos($aPage['component_file'], '/'));
        
        // Display error if the module is not installed or enabled.
        if($bModulePage && !Linko::Module()->isModule($sModule))
        {
            return Linko::Module()->set('_404_');    
        }

        Linko::Template()
            ->setTitle($this->getSetting('page.site_title'))
			->setTitle($aPage['title'])
			->setBreadcrumb(array(), $aPage['title'])
			->setMetaDescription($aPage['meta_description'])
			->setMetaKeywords($aPage['meta_keywords'])
			->setMeta(array('keywords' => 'this, is, my, page'))
            ->setHeader(Html::script('//Linko.cookie.set("dropmenu", "colio");
            Linko.cookie.get("dropmenu");'), 100);
        
        // Set Layout
		if($aPage['page_layout'] != NULL && Linko::Model('Theme')->isLayout($aPage['page_layout']))
		{
			Linko::Template()->setLayout($aPage['page_layout']);
		}

		// if there is a home.phtml file in theme, lets use it.
		if($bIsHome)
		{
			if($oUserAuth->isUser() && Linko::Template()->isLayout('home-member'))
			{
				Linko::Template()->setLayout('home-member');
			}
			elseif(!$oUserAuth->isUser() && Linko::Template()->isLayout('home-guest'))
			{
					Linko::Template()->setLayout('home-guest');
			}
			elseif(Linko::Template()->isLayout('home'))
			{
				Linko::Template()->setLayout('home');
			}
		}
               
        if($bModulePage)
        {
            Linko::Module()->set($aPage['component_file'], $oRoute->args);
        }

		Linko::Plugin()->call('page.controller_view_end', $aPage['page_id']);

		Linko::Template()->setVars(array(
				'aPage' => $aPage
			)
		);		
	}
}

?>