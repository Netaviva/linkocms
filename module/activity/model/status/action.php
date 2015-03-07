<?php

class Activity_Model_Status_Action extends  Linko_Model
{
    public function add($aVals, $iId = null)
    {
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

        if(!Arr::hasKeys($aVals, 'status'))
        {
            return false;
        }

        if($bUpdate)
        {
            $iId = Linko::Database()->table('activity_status')
                ->update(array(
                    'status' => $aVals['status']
                ))
                ->where('status_id', '=', $iId)
                ->query()
                ->getInsertId();

            Linko::Plugin()->call('activity.update_status', $iId, $aVals);

            return $iId;
        }
        else
        {
            $iId = Linko::Database()->table('activity_status')
                ->insert(array(
                    'user_id' => $iUserId,
                    'status' => $aVals['status'],
                    'time_created' => Date::now()
                ))
                ->query()
                ->getInsertId();

            Linko::Plugin()->call('activity.add_status', $iId, $aVals);

            return $iId;
        }

        return false;
    }
}