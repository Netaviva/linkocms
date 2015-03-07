<?php

class Activity_Model_Action extends  Linko_Model
{
    public function add($aVals, $iId = null)
    {
	    // $aVals - item_id, feed_var, module_id, user_id:optional
        if(!Linko::Model('User/Auth')->isUser())
        {
            return false;
        }

        $bUpdate = false;

        if($iId)
        {
            $bUpdate = true;
        }

        $iUserId = isset($aVals['user_id']) ? $aVals['user_id'] : Linko::Model('User/Auth')->getUserId();

        if($bUpdate)
        {

        }
        else
        {
            if(!Arr::hasKeys($aVals, 'item_id', 'feed_var', 'module_id'))
            {
                return false;
            }

            $iId = Linko::Database()->table('activity_feed')
                ->insert(array(
                    'module_id' => $aVals['module_id'],
                    'user_id' => $iUserId,
                    'feed_var' => $aVals['feed_var'],
                    'item_id' => $aVals['item_id'],
                    'time_created' => Date::now()
                ))
                ->query()
                ->getInsertId();

            Linko::Plugin()->call('activity.add_feed');

            return $iId;
        }
    }

    public function delete($iId)
    {
        Linko::Database()->table('activity_feed')
            ->delete()
            ->where('activity_id', '=', $iId)
            ->query();

        return true;
    }

    /**
     * Adds a comment to an activity
     *
     * @param int $iActivityId
     * @param string $sComment
     * @return int comment id
     */
    public function addComment($iActivityId, $sComment)
    {
        return Linko::Model('Comment/Action')->addComment(array(
            'module_id' => 'activity',
            'item_id' => $iActivityId,
            'comment' => $sComment
        ));
    }
}