<?php

defined('LINKO') or exit();

class Follow_Model_Follow extends Linko_Model
{
    //total limit for each pagin
    public $limit = 10;
    /*
     * method to check if user is following a user
     * @param $ref_id of end user
     * @param $user_id
     * @return boolean
     */
    public  function isFollowing($sRefId, $iUserId =null)
    {
        $iUserId = (empty($iUserId)) ? Linko::Model('User/Auth')->getUserId() : $iUserId;


        if($iUserId == $sRefId || !Linko::Model('User/Auth')->isUser()) return true;

        $iCount = Linko::Database()->table('follow')
            ->select()
            ->where('user_id' ,'=', $iUserId)->where('reference_id', '=', $sRefId)
            ->query()->getCount();

        if($iCount>0) return true;

        return false;

    }

    /*
     * Method to count user following and followers
     * @param $sType either follow or followers
     * @retutn int
     *
     */
    public function count($sType, $iUserId=null)
    {
        $oQuery = Linko::Database()->table('follow')->select()
                                   ->where(($sType == 'follow')? 'user_id' : 'reference_id', '=', $iUserId)->query();

        return $oQuery->getCount();
    }

    public function getList($iUserId,$sType, $iPage)
    {
        return Linko::Database()->table('follow')
                                ->select(($sType == 'follow') ? 'user_id' : 'reference_id')
                                ->where(($sType == 'follow') ? 'user_id' : 'reference_id', '=', $iUserId)
                                ->query()
                                ->paginate($iPage , $this->limit);
    }

    public function getRecent($sType = 'follow', $iLimit = 5)
    {
        $iUserId =(Linko::Model('profile')->isProfile()) ? Linko::Model('profile')->getOwnerId() : Linko::Model('user/auth')->getUserId();

        $aRow = Linko::Database()->table('follow')
                                  ->select(($sType == 'follow') ? 'user_id' : 'reference_id')
                                  ->where(($sType == 'follow') ? 'user_id' : 'reference_id', '=', $iUserId)
                                  ->limit($iLimit)
                                  ->query()->fetchRows();
        $aResult = array();

            foreach($aRow as $k)
            {
                $iUserId = ($sType =='follow') ? $k['user_id'] : $k['reference_id'];
                $aResult[] = Linko::Model('User')->getUser($iUserId);
            }



        return $aResult;
    }
}
?>