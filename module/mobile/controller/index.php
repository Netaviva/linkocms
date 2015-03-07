<?php

/**
 * @package Mobile Module
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class Mobile_Controller_Index extends Linko_Controller
{
    public function main()
    {
        $sUri = $this->getParam('uri');

        // Set route namespace to mobile
        Linko::Router()->setKey('mobile');

        // Set route base to mobile
        // All routes created after this will have a 'mobile' prefix
        Linko::Router()->setBase('mobile');

        // Here we start building the routes
        // First: get all the routes under the "page" namespace.
        $aRoutes = Linko::Router()->getRoutes('page');

        // We then take all the routes and try to set them again
        // but this time in the 'mobile' namespace
        // with each route having a prefix of 'mobile'
        foreach($aRoutes as $sId => $oRoute)
        {
            if($sId == 'mobile:core')
            {
                continue;
            }

            // Set routes
            /** * @var $oRoute Linko_Route */
            Linko::Router()->add($oRoute->getRawRegex(), array(
                'id' => $sId,
                'controller' => $oRoute->getController(),
                'rules' => $oRoute->getRules()
            ));
        }

        // Just adding some extra routes
        Linko::Router()->add('/', array(
            'id' => 'mobile:home',
            'controller' => 'mobile/home'
        ));

        Linko::Router()->add('dashboard', array(
            'id' => 'mobile:dashboard',
            'controller' => 'mobile/dashboard'
        ));

        // Re-route the request
        $oRoute = Linko::Router()->route($sUri);

        // Set the controller based on the route matched...
        // Also pass the arguments to it.
        // @todo Implement the error controller
        Linko::Module()->set($oRoute->controller, $oRoute->args);

        // Sets the theme type to 'mobile'
        // This will allow theme developers to create mobile specific themes
        // by just creating a folder 'mobile' in their theme mobile just
        // like 'frontend' and 'backend' is done.
        Linko::Template()->setType('mobile');

        // Set the mobile fixing stylesheet.
        // This style will be used to fix style sheet of
        // modules that has not yet been mobile optimized.
        Linko::Template()->setStyle('mobile.css', 'module_mobile');

        // Of course, if the user is using this module but does not have
        // a theme with mobile support yet. This checks if the current theme
        // has mobile support and if it doesnt.....
        if(!Linko::Template()->isLayout('template'))
        {
            // set layout directory to this module layout
            // this will make the system load layouts from this module
            Linko::Template()->setLayoutDirectory('module_mobile');

            // remove stylesheets
            // We remove these becos they are meant for frontend and not mobile
            Linko::Template()->clearStyle('style.css', 'theme_css');
            Linko::Template()->clearStyle('common.css', 'theme_css');

            // set our own default style sheet.
            Linko::Template()->setStyle('reset.css', 'asset_css');
            Linko::Template()->setStyle('style.css', 'module_mobile');

            // set the template type to null
            // so when its loading layout from module, it does not
            // look for it in a 'mobile' directory again.
            Linko::Template()->setType(null);
        }
    }
}