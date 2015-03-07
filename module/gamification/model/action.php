<?php



class Gamification_Model_Action extends Linko_Model
{
    public $table='gamification';
    public function add($iUserid)
    {
        if(!empty($iUserid))
        {
            Linko::Database()->table('gamification')->insert(
                array
                (
                    'user_id'=>$iUserid,
                    'time'=>time(),
                    'point' => Linko::Module()->getSetting('gamification.default_point')
                ))->query();
        }
        return true;
    }

}