<?php

class Activity_Model_Status extends  Linko_Model
{
    public function getStatus($iId)
    {
        return Linko::Database()->table('activity_status')
            ->select()
            ->where('status_id', '=', $iId)
            ->query()
            ->fetchRow();
    }
}