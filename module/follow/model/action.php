<?php

defined('LINKO') or exit();

class Follow_Model_Action extends Linko_Model
{
    public function add($sRefId, $sModuleId = 'user', $iUserId = null)
    {
        $iUserId = (empty($iUserId)) ? Linko::Model('User/Auth')->getUserId() : $iUserId;

        $query = Linko::Database()->table('follow')
                        ->insert(array(
                        'time_created' => time(),
                        'user_id' => $iUserId,
                        'reference_id' => $sRefId,
                        'module_id' => $sModuleId
                        ))->query();

        return $query->getInsertId();
    }

}