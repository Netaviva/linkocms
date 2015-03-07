<?php

/**
 * Core File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Messages_Model_Messages extends Linko_Model
{
    /*
     * Method to get a particular user messagegs details
     * @parram message from userid
     * @return array
     */
    public function getMessagesList($iUserId)
    {
        $this->currentMessageEntity = $iUserId;
        $aQuery = Linko::Database()->table('messages')
            ->select()->where('to_user_id', '=', Linko::Model('User/Auth')->getUserId())
            ->where('from_user_id', '=', $iUserId)
            ->orWhere(function ($oBject){

                $oBject->where('from_user_id', '=', Linko::Model('User/Auth')->getUserId())
                    ->where('to_user_id', '=', Linko::Model('messages')->currentMessageEntity);

             })
            ->query()->fetchRows();

        $aResult = array();
        $i =0;
        foreach($aQuery as $key => $aValue)
        {

            $aResult[$i] = array();
            $aResult[$i]['message-details'] = $aValue;
            $aResult[$i]['user-details'] = Linko::Model('user')->getUser($aValue['from_user_id']);
            $i++;
        }
        //arr::dump($aResult);
        return $aResult;

    }
    public function getMessageDetails($iId)
    {
        $aQuery = Linko::Database()->table('messages')
            ->select()
            ->where('message_id', '=', $iId)
            ->query()->fetchRows();

        $aResult = array();
        $i =0;
        foreach($aQuery as $key => $aValue)
        {

            $aResult[$i] = array();
            $aResult[$i]['message-details'] = $aValue;
            $aResult[$i]['user-details'] = Linko::Model('user')->getUser($aValue['from_user_id']);
            $i++;
        }
        //arr::dump($aResult);
        return $aResult;

    }

    /*
     * Method to get each user conversation entities
     * @return array
     */
    public function getMessagesEntity( $limit = null)
    {
          $aQuery = Linko::Database()->table('messages')
              ->select()
              ->where('to_user_id', '=', Linko::Model('User/Auth')->getUserId())
              ->orWhere('from_user_id', '=', Linko::Model('User/Auth')->getUserId())
              ->order('message_time desc');

        if(!empty($limit))
          {

              $aQuery = $aQuery->limit($limit);
          }
          $aQuery = $aQuery->query()->fetchRows();

          $aResult = array();

          $loginUserId = Linko::Model('user/auth')->getUserId();

            //arr::dump($aQuery);
          foreach($aQuery as $key => $aValue)
          {
              $userId =($aValue['from_user_id'] == $loginUserId ) ? $aValue['to_user_id'] : $aValue['from_user_id'];
              if(!isset($aResult[$userId]))
              {
                  $aResult[$userId] = array();
                  $aResult[$userId]['message-details'] = $aValue;

                  $aResult[$userId]['user-details'] = Linko::Model('user')->getUser($userId);
              }

          }
          return $aResult;

    }

    /*
     * method to mark a message read
     */
    public function markRead($messageId)
    {
        Linko::Database()->table('messages')
            ->update(array('message_read' => 1))
            ->where('to_user_id', '=', Linko::Model('User/Auth')->getUserId())
            ->where('message_id', '=', $messageId)
            ->query();
        return true;

    }

    public function getLastMessageUser()
    {
        $query = Linko::Database()->table('messages')
            ->select()
            ->where('to_user_id', '=', Linko::Model('user/auth')->getUserId())
            ->orWhere('from_user_id', '=', Linko::Model('user/auth')->getUserId())
            ->order('message_time desc')
            ->query()->fetchRow();
        if(empty($query)) return false;
        return ($query['from_user_id'] == Linko::Model('user/auth')->getUserId()) ? $query['to_user_id'] : $query['from_user_id'];

    }

    public function countUnread()
    {
        ///return 2;
        return Linko::Database()->table('messages')
            ->select()
            ->where('to_user_id' ,'=', Linko::Model('user/auth')->getUserId())
            ->where('message_read', '=', 0)
            ->query()->getCount();
    }
}