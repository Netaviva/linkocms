<?php

/**
 * Core File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Messages_block_Messages_mini extends Linko_Controller
{
    public function main()
    {
        $aMessages = $this->getParam('messages');

        if(empty($aMessages))
        {
            $iLimit = $this->getParam('message_mini_limit');
            $aMessages = Linko::Model('messages')->getMessagesEntity($iLimit);
        }

        Linko::Template()->setVars(array('aMiniMessages' => $aMessages));
    }
}