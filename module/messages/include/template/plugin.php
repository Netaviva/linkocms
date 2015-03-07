<?php

class Template_Plugin_Messages
{
    public function start($aParams = array())
    {
        /**
         * @var boolean $add_mini add mini message like 5 recent messages default is false
         * @var int $mini_limit mini messagges list limit
        */
        extract(array_merge(array(
            'add_mini' => false,
            'mini_limit' => 5,
        ), $aParams));

        if(!Linko::Model('user/auth')->isUser())
        {
            return false;
        }
        $sHtml = Html::openTag('div', array('class' => 'message-notification'));
        
        $sHtml .= Html::openTag('span', array( 'id' => 'message-notification-span', 'class' => 'count'));
                
        $iNo = Linko::Model('messages')->countUnread();
        if($iNo>0)
        {
            $sHtml .= $iNo;
        }

        $sHtml.= Html::closeTag('span');
        $sHtml.= Html::closeTag('div');
        
        $sHtml = Html::link($sHtml, Linko::Url()->make('messages:index'), array('id' => 'message-notification-link'));

        if($add_mini)
        {
            $sHtml .= Html::openTag('div', array('id' => 'message-mini-container', 'class' => 'message-noticontent'));
            $sHtml .= Linko::Template()->getBlock('messages/messages_mini',array('messages' =>Linko::Model('messages')->getMessagesEntity($mini_limit)));

            $sHtml .= Html::closeTag('div');

        }
        echo $sHtml;


    }

}