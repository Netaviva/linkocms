<?php

/**
 * Core File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Messages_Model_Action extends Linko_Model
{

    public function save($ifromUserId, $iToUserId, $sText, $iAdmin = 0)
    {
       $query = Linko::Database()->table('messages')
                        ->insert(array
        (
            'from_user_id' => $ifromUserId,
            'to_user_id' => $iToUserId,
            'message_content' => $sText,
            'message_content' => $sText,
            'message_time' => time(),
            'message_from_admin' => $iAdmin
        ))->query();

        Linko::Plugin()->call('messages.save',$query->getInsertId());

        return $query->getInsertId();
    }
}