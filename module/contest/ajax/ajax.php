<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage contest : ajax - ajax.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Contest_Ajax extends Linko_Ajax
{
    function playContest() {
        $id=Input::post('id');
        $winner=Input::post('winner');
        $point=Input::post('point');
        
        if(Linko::Model('Contest/Match')->participate($id,$winner,$point))
        {
            echo json_encode(array('success'=>1));
        }
        else
        {
            echo json_encode(array('success'=>0));
        } 
    }
    
    function validateMatch()
    {
        $id=Input::post('id');
        
        Linko::Model('Contest/Match')->validate($id,Input::post('result'),Input::post('score'));
    }
    
    function __call($name,$args)
    {
        switch($name)
        {
            case 'history';
                Linko::Template()->getTemplate('contest/controller/history/content');
            break;
            default:
            
                Linko::Template()->getTemplate('contest/controller/match/content');
            break;
        }
    }
    
    function matchParticipate()
    {
        $id=Input::post('id');
        $winner=Input::post('winner');
        $point=Input::post('point');
        
        if(Linko::Model('Contest/Match')->participate($id,$winner,$point))
        {
            echo json_encode(array('success'=>1));
        }
        else
        {
            echo json_encode(array('success'=>0));
        }
        
        
    }
    public function endMatch()
    {
        $id=Input::post('id');
        Linko::Model('Contest/Contest')->offVisible($id);
    }
    
    public function openMatch()
    {
        $id=Input::post('id');
        Linko::Model('Contest/Contest')->onVisible($id);
    }
    
    public function more()
    {
        $c=Input::get('current');
        switch($c)
        {
            case 'history';
                foreach(Linko::Model('Contest/Match')->getMoreHistory() as $k=>$v)
                {
                    Linko::Template()->getTemplate('contest/controller/history/single-content',array('contest'=>$v));
                }
            break;
        }
    }
    
}
?>