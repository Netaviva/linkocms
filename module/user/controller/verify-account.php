<?php

class User_Controller_Verify_Account extends Linko_Controller
{
    public function main()
    {
        $sHash = $this->getParam('hash');

        if(Linko::Model('User/Auth')->isVerified())
        {
            Linko::Response()->redirect();
        }

        if(Linko::Model('User/Action')->verifyAccountByHash($sHash))
        {
            Linko::Flash()->success(Lang::t('user.account_verified'));
            Linko::Response()->redirect();
        }
    }
}