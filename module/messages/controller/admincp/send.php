<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage blog : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class messages_controller_admincp_send extends Linko_Controller
{
    public function main()
    {
        if($aVal = Input::post('val'))
        {

            $sUsername = $aVal['username'];
            $aUserDetails = Linko::Model('user')->getUser($sUsername);
            $sTest = $aVal['text'];

            if(empty($aUserDetails) || empty($sTest))
            {
                //flash admin invalid usersnme
                Linko::Flash()->error('Failed to send message due invalid username or content is empty');
            }
            else
            {
                Linko::Model('messages/action')->save(0,$aUserDetails['user_id'], $sTest, 1);
                Linko::Flash()->success("Messages sent successfully.");
            }
        }

        Linko::Template()
            ->setBreadcrumb(array(
            'Modules',
        ), 'Send Message to Users')
            ->setTitle('Send Messages');

    }
}