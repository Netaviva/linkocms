<?php

class Gamification_Model_Point extends Linko_Model
{
    public $table='gamification';


    public function __construct()
    {
        $this->table=Linko::Model('Gamification')->table;


    }

    /*
     * Method to confirm if user activate point for users
     *
     */
    public function usePointSystem()
    {
        if(Linko::Module()->getSetting('gamification.enable_point_system')) return true;

        return false;
    }
    
    //function to get user points
    public function get($iUserid)
    {
        if(empty($iUserid)) return 0;

        //ensure user exists
        Linko::Model('Gamification')->exists($iUserid);

        $point=Linko::Database()->table($this->table)->select('point')->where('user_id','=',$iUserid)->query()->fetchRow();

        if($point['point']<0) return 0;

        return $point['point'];
    }


    //function to add points
    function add($iUserid,$amount=0)
    {
        if (empty($amount) || !$this->usePointSystem()) return true;//no need for updating it

        //ensure int of amount

        $userDetails=Linko::Model('Gamification')->get($iUserid);
        Linko::Database()->table($this->table)->update(array('point'=>$userDetails['point']+$amount))->where('user_id','=',$iUserid)->query();
        return true;
    }

    //function to remove point from user points
    function remove($iUserid,$amount=0)
    {
        if (empty($amount)) return true;//no need for updating it

        //ensure int of amount

        $userDetails=Linko::Model('Gamification')->get($iUserid);
        Linko::Database()->table($this->table)->update(array('point'=>$userDetails['point'] -$amount))->where('user_id','=',$iUserid)->query();
        return true;

    }
}
?>