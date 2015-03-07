<?php

class Gamification_Model_Activity extends Linko_Model
{
    public $table='gamification_activity';
    
    //function to to increase activity counter
    public function increment($type,$iUserid,$no=0)
    {
        //ensure existence
        $this->add($type,$iUserid);

        $details=$this->get($type,$iUserid);

        Linko::Database()->table($this->table)->update(array('counter'=>$details['counter'] + $no))->where('user_id','=',$iUserid)->where('type','=',$type)->query();
        return true;
            
        
    }
    
    public function decrement($type,$iUserid,$no=0)
    {
        //ensure existence
        $this->add($type,$iUserid);
        
        $details=$this->get($type,$iUserid);
        Linko::Database()->table($this->table)->update(array('counter'=>$details['counter'] -$no))->where('user_id','=',$iUserid)->where('type','=',$type)->query();
            return true;
        
    }
    
    //function to get user activity details
    public function get($type,$iUserid)
    {
        return Linko::Database()->table($this->table)->select()
            ->where('user_id','=',$iUserid)
            ->where('type','=',$type)
            ->query()->fetchRow();
    }
    
    public function exists($type,$iUserid)
    {
        if(empty($iUserid) || empty($type)) return false;

        $count = Linko::Database()->table($this->table)->select()
            ->where('user_id','=',$iUserid)
            ->where('type','=',$type)
            ->query()->getCount();

        if($count>0) return true;
        return false;

    }
    
    public function add($type,$iUserid)
    {
        if(!$this->exists($type,$iUserid))
        {
            Linko::Database()->table($this->table)->insert(array('user_id'=>$iUserid,'type'=>$type,'time'=>time()))->query();
            return true;
        }   
        
    }
    
    //function to get current count
    function count($type,$iUserid)
    {
        //also ensure existence
        $this->add($type,$iUserid);
        $g=$this->get($type,$iUserid);
        if(!empty($g)) return $g['counter'];
        
        return 0;
    }
}
?>