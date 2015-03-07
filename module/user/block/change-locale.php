<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : block - change-locale.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Block_Change_Locale extends Linko_Controller
{
    public function main()
    {
        $aLanguages = Linko::Model('Locale/Language')->getLanguages();

        $iUserId = Linko::Model('User/Auth')->getUserId();

        /**
         * @todo allow guest change language
         * guest locale will be stored in session.
         */
        if(Linko::Model('User/Auth')->isUser() && ($sLocale = Input::post('change_locale')))
        {
            if(Linko::Model('User/Action')->updateField($iUserId, 'locale_id', $sLocale))
            {
                Linko::Flash()->success('Locale Changed.');
                Linko::Response()->redirect('self');
            }
        }
        
        Linko::Template()->setVars(array(
            'aLanguages' => $aLanguages
        ));
    }
}

?>