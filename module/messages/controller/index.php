<?php

/**
 * Core File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Messages_Controller_Index extends Linko_Controller
{
    public function main()
    {
        $iUserId = $this->getParam('userid');

        if(!is_numeric($iUserId) && !empty($iUserId))
        {
            $iUserId = Linko::Model('user')->getUser($iUserId);
            //sarr::dump($iUserId);
            $iUserId = (!empty($iUserId)) ? $iUserId['user_id'] : 0;

        }

        //if the userid is emppty we make use of the last user for now later we allow user to send message to there followers or following
        if($iUserId != '0' && empty($iUserId))
        {
            $iUserId = Linko::Model('messages')->getLastMessageUser();

        }


        //arr::dump(Linko::Model('Messages')->getMessagesList($iUserId));

        Linko::Template()
            ->setVars
            (
                array
                (
                    'aMessages' => Linko::Model('Messages')->getMessagesList($iUserId),
                    'aMessagesEntity' => Linko::Model('Messages')->getMessagesEntity(),
                    'aUser' => Linko::Model('user')->getUser($iUserId)
                )
            )
            ->setStyle('messages.css', 'module_messages')
            ->setTranslation(array('messages.send_message', 'messages.send_error','messages.sent_message'))
            ->setScript('messages.js', 'module_messages');


    }
}