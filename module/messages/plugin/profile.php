<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage activity : plugin/profile.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */

class Messages_Plugin_Profile
{
    public function tools_start()
    {
        if(Linko::Model('profile')->getOwnerId() != Linko::Model('user/auth')->getUserId() )
        {
            Linko::Template()->setScript('send-message.js', 'module_messages');
            Linko::Template()->setStyle('send-message.css', 'module_messages')
                ->setTranslation(array('messages.send_message', 'messages.send_error','messages.sent_message'));
            echo Linko::Template()->getTemplate('messages/controller/_layout/send-button',array('iUserId' => Linko::Model('profile')->getOwnerId()),true);

        }
    }

 }
?>