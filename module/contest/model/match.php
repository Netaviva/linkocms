<?php

class Contest_Model_Match extends Linko_Model
{
    private $limit=10;
    
        function participate($iContestId, $iTeamGuess, $iContestPoint) {
            
        if(Linko::Model('Contest/Contest')->hasParticipated($iContestId)){
            return false;
        }
        
        $iUserPoint = Linko::Model('Gamification/Point')->get(Linko::Model('User/Auth')->getUserId());
        
        if($iUserPoint < $iContestPoint) return false;
        
        //deduct points fron user points
        Linko::Model('Gamification/Point')->remove(Linko::Model('User/Auth')->getUserId(),$iContestPoint);
        //return false;
        
        $oQueryId = Linko::Database()->table('contestant')->insert(array(
            'user_id' => Linko::Model('User/Auth')->getUserId(),
            'point_amount' => $iContestPoint,
            'contest_id' => $iContestId,
            'team_guess' => $iTeamGuess,
            'timestamp'=>time()
            ))
                ->query()
                ->getInsertId();
        //plugin for call
        Linko::Plugin()->call('contest.match', $oQueryId);
        
       $this->updateParticipants($iContestId, $iTeamGuess); 
       
        return true;
    }
    
    function updateParticipants($iContestId, $iTeamGuess)
    {
        $de = $this->get($iContestId);
        
        $array=array();
        switch($iTeamGuess)
        {
            case 'a';    
                $var3=$de['var3'];
                $var3=$var3 +1;
                $array=array('var3'=>$var3);
            break;
        
            case 'b';
                $var4=$de['var4'];
                $var4=$var4 +1;
                $array=array('var4'=>$var4);
            break;
        
            case 'c';
                $var5=$de['var5'];
                $var5=$var5 +1;
                $array=array('var5'=>$var5);
            break;
        
        }
        
        if(!empty($array)) Linko::Database()->table('contest')->update($array)->where('id','=',$id)->query();
        
    }
   
    function getAdminList()
    {
        return Linko::Database()->table('contest')->select()->where('type','=','match')->query()->fetchRows();
    }
    
    function getList()
    {
        
        return Linko::Database()->table('contest')->select()->where('type','=','match')->where('visible','!=',0)->order('time desc')->query()->fetchRows();
    }
    
    
    
    function get($id)
    {
        return Linko::Database()->table('contest')->select()->where('id','=',$id)->query()->fetchRow();
    }
    
    //get history
    public function getHistoryList($limit=null,$offset=0,$last=null)
    {
        $limit=(empty($limit)) ? $this->limit : $limit;
        $last=(empty($last)) ? $this->limit : $last;
        Linko::Session()->set('history-last-query',$last);
        
        return Linko::Database()->table('match_history')->select()->order('time desc')->limit($limit)->offset($offset)->query()->fetchRows();
    }
    
    //function to get more history
    public function getMoreHistory()
    {
        $last=Linko::Session()->get('history-last-query');
        return $this->getHistoryList($this->limit,$last,$last+$this->limit);
        
    }
    
    function validate($id,$result,$score)
    {
        $db=Linko::Database()->table('contestant')->select()->where('contest_id','=',$id)->query()->fetchRows();
        
        $contestDetails=$this->get($id);
        foreach($db as $k=>$v)
        {
            $uid=$v['uid'];
            $point=$v['point'];
            $anwser=$v['var1'];
            if($result==$anwser)
            {
                //user wins yeeeeeeeeeh
                //now we got give user point that is half of the point user used and a notification
                Linko::Model('Gamification/Point')->add($uid,$point + $point/2);
                Linko::Plugin()->call('contest.matchWin',$uid,$contestDetails,$point + $point/2);
                //notification that user win the contest be the match 
                Linko::Model('Notification/Add')->add(array(
                    'from_id'=>'admin',
                    'to_id'=>$uid,
                    'type'=>'contest-result',
                    'var1'=>$contestDetails['var1'],
                    'var2'=>$contestDetails['var2'],
                    'var3'=>1,//that is user wins the contest
                    'var4'=>$point + $point/2//what user have earn
                ));
                
            }
            else
            {
                ///contest looser
                Linko::Plugin()->call('contest.matchLoose',$uid,$contestDetails,$point);
                //user loose haahaaha on notification for loosing the contest
                Linko::Model('Notification/Add')->add(array(
                    'from_id'=>'admin',
                    'to_id'=>$uid,
                    'type'=>'contest-result',
                    'var1'=>$contestDetails['var1'],
                    'var2'=>$contestDetails['var2'],
                    'var3'=>0//that is user wins the contest
                ));
                
            }
            
            //increment user level for participating in the contest
            Linko::Model('Gamification/Activity')->increment('user-level',$uid,10);
            
        }
        
        $this->addhistory($contestDetails,$result,$score);
            //we have to delete the contestants and the contest
            Linko::Database()->table('contestant')->delete()->where('contest_id','=',$id)->query();
            Linko::Database()->table('contest')->delete()->where('id','=',$id)->query();
        
        return true;
    }
    
    
    //function to add match history
    public function addHistory($details,$result,$score)
    {
        $time=$details['end_time'];
        $info=array('a'=>unserialize($details['var1']),'b'=>unserialize($details['var2']));
        $votes=array('a'=>$details['var3'],'b'=>$details['var4'],'c'=>$details['var5']);
        $club1=$info['a']['name'];
        $club2=$info['b']['name'];
        Linko::Database()->table('match_history')
                         ->insert(array(
                            'time'=>time(),
                            'club1'=>$club1,
                            'club2'=>$club2,
                            'score'=>$score,
                            'winner'=>$result,
                            'details'=>serialize($info),
                            'date'=>$time,
                            'votes'=>serialize($votes)
                         
                         ))->query();
        return true;                         
    }
}
?>