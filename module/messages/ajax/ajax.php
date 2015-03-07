<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Messages_Ajax extends Linko_Ajax
{
    public function send()
    {
        $iUserId = $this->getParam('userid');
        $sMessageText = $this->getParam('text');

        $postId = Linko::Model('messages/action')->save(Linko::Model('User/Auth')->getUserId(), $iUserId , $sMessageText);

        //arr::dump(Linko::Model('messages')->getMessageDetails($postId));
        foreach(Linko::Model('messages')->getMessageDetails($postId) as $k => $aValue)
        {
            $this->output(Linko::Template()->getTemplate('messages/controller/_layout/message_content',array('aContent' => $aValue),false));
        }
    }
    public function entityMessages()
    {
        $iUserId = $this->getParam('userid');

        $sContent = "";

        foreach(Linko::Model('Messages')->getMessagesList($iUserId) as $key => $value)
        {
            $sContent .= Linko::Template()->getTemplate('messages/controller/_layout/message_content',array('aContent' => $value),false);
        }

        $this->output($sContent);

    }
}
