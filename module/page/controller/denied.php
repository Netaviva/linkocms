<?php

/**
 * @package Page Module
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class Page_Controller_Denied extends Linko_Controller
{
    public function main()
    {
        $sTitle = 'Home';

        if(!Linko::Model('Page')->isHome())
        {
            $sTitle = "Not Permitted";
        }

        Linko::Template()->setTitle($sTitle);

        $sMessage = $this->getSetting('page.permission_denied_message');

        if(!Linko::Model('User/Auth')->isUser())
        {
            switch($this->getSetting('page.on_guest_permission_denied_action'))
            {
                case 'redirect':
                    $aPage = Linko::Model('Page')->getPage((int)$this->getSetting('page.guest_permission_denied_redirect_url'));

                    if(isset($aPage['page_id']))
                    {
                        Linko::Response()->redirect($aPage['page_url']);
                    }
                    break;
                case 'display_deny_message':

                default;
                   break;
            }
        }

        Linko::Template()->setVars(array(
            'sMessage' => $sMessage
        ));
    }
}