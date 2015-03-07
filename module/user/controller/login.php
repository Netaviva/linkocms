<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : login.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Login extends Linko_Controller
{
	public function main()
	{
		if(($aVals = Input::post('val')) && (Arr::hasKeys($aVals, array('username', 'password'))))
		{
			Linko::Validate()->set('login_form', array(
					'username' => array(
						'function' => 'required',
						'error' => 'You must enter a username',
					),
					'password' => array(
						'function' => 'required',
						'error' => 'You must enter a password',
					)

				)
			);

			if(Linko::Validate()->isValid($aVals))
			{
                list($iUserId, $sMsg) = Linko::Model('User/Auth')->login($aVals['username'], $aVals['password'], false);

				if($iUserId)
				{
					$sRedirect = '';

                    if(!Linko::Model('User')->isVerified($iUserId))
                    {
                        Linko::Error()->set($sMsg);
                    }
                    else
                    {
                        if($iPage = $this->getSetting('user.page_redirect_after_login'))
                        {
                            $aPage = Linko::Model('Page')->getPage((int)$iPage);

                            $sRedirect = Linko::Url()->make(($aPage['route_id'] ? $aPage['route_id'] : $aPage['page_url']));
                        }

                        Linko::Flash()->success($sMsg);
                        Linko::Response()->redirect($sRedirect);
                    }
				}
                else
                {
                    Linko::Error()->set($sMsg);
                }
			}			
		}
	}
}

?>