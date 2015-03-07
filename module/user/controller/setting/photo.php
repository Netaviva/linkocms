<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage user : setting\photo.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class User_Controller_Setting_Photo extends Linko_Controller
{
    public function main()
    {
        Linko::Model('User/Auth')->isUser(true);

        $aUser = Linko::Model('User/Auth')->getUser();

        if(Input::post('upload_photo'))
        {
            // delete old uploaded files
            if($aUser['user_photo'] != null)
            {
                foreach(Dir::getFiles(DIR_USER_PHOTO . pathinfo($aUser['user_photo'], PATHINFO_DIRNAME) . DS, false, array($aUser['username'] . '_?(\d+)?.(png|jpeg|jpg|gif)')) as $sFile)
                {
                    File::delete($sFile);
                }
            }

            Linko::Upload()->setAllowedType(array('jpg', 'jpeg', 'png', 'gif'))
                ->setAllowedMime(array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'))
                ->setFilename($aUser['username'])
                ->setMaxSize(5, 'MB')
                ->setOverwrite(true)
                ->setDestination(APP_PATH);

            $sPath = date('Y') . '/' . date('m') . '/';

            if(Linko::Upload()->load('image'))
            {
                if(!Dir::exists(DIR_USER_PHOTO . $sPath))
                {
                    Dir::create(DIR_USER_PHOTO . $sPath);
                }

                if(Linko::Upload()->save(DIR_USER_PHOTO . $sPath))
                {
                    $sName = Linko::Upload()->getFilename();
                    $sExt = Linko::Upload()->getExtension();

                    Linko::Database()->table('user')
                        ->update(array('user_photo' => ($sPath . $sName . '_%d.' . $sExt)))
                        ->where('user_id', '=', $aUser['user_id'])
                        ->query();

                    Linko::Image()->load(Linko::Upload()->getFile());

                    foreach(array(20, 50, 100, 150, 200) as $iSize)
                    {
                        Linko::Image()
                            ->resize($iSize, $iSize, true)
                            ->save(DIR_USER_PHOTO . $sPath . $sName . '_' . $iSize . '.' . $sExt);

                        Linko::Image()->reset();
                    }

                    Linko::Flash()->success(Lang::t('user.profile_picture_uploaded'));
                    Linko::Response()->redirect('self');
                }
                else
                {
                    foreach(Linko::Upload()->getErrors() as $sError)
                    {
                        Linko::Error()->set($sError);
                    }
                }
            }
        }

        Linko::Template()
            ->setVars(array(
                'aUser' => $aUser
            ));
    }
}

?>