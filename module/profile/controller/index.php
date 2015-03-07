<?php

defined('LINKO') or exit();

class Profile_Controller_Index extends Linko_Controller
{
    public function main()
    {
        define('USER_PROFILE', true);

        $sUser = $this->getParam('username');

        if($sUser == 'me')
        {
            $sUser = Linko::Model('User/Auth')->getUserBy('username');
        }
        $sSlug = $this->getParam('slug');

        Linko::Router()->setBase('profile')
            ->setKey('profile');

        Linko::Template()
            ->setStyle('profile.css', 'module_profile');

        Linko::Model('profile')->set($sUser);

        if(!Linko::Model('profile')->exists())
        {
            return Linko::Module()->set('_404_', array(
                'message' => 'This user does not exist.'
            ));

        }
        Linko::Model('profile')->setController($sSlug);

    }
}
