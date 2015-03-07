<?php

class Gamification_Model_Badge extends Linko_Model
{
    private $aBadges = array();
    
    public $table = 'gamification_badge';
    
    public function __construct()
    {
    }
    

    //method to set all the badges it will be set by the system
    public function set($aBadges=array())
    {
        if(!empty($aBadges))
        {
            $this->aBadges=$aBadges;
        }
        return true;
    }
    
    //function to get user total badges
    public function total($iUserid)
    {
        if(empty($iUserid)) return 0;
        
        return Linko::Database()->table($this->table)->select()->where('user_id','=',$iUserid)->query()->getCount();

    }
    
    //function to add badge to user
    function add($badgeId,$iUserid,$callback=null)
    {
        if(empty($iUserid)) return false;


        //must not already exist
        if(!$this->hasBadge($badgeId,$iUserid))
        {
            //continue inserting
            Linko::Database()->table($this->table)->insert(array('user_id'=>$iUserid,'badge_id'=>$badgeId,'time'=>time()))->query();
            
            if(!empty($callback))
            {
                call_user_func($callback,$badgeId,$iUserid);
            }
            return true;
        }
        
    }
    
    //function to check user already has a badge
    public function hasBadge($badgeId,$iUserid)
    {
        if(empty($iUserid)) return false;
        
        $count=Linko::Database()->table($this->table)->select()->where('user_id','=',$iUserid)->where('badge_id','=',$badgeId)->query()->getCount();
        if($count>0) return true;
        return false;
    }
    
    
    //function to remove user from badge table
    public function remove($badge,$iUserid)
    {
        if(empty($iUserid)) return false;
        
        Linko::Database()->table($this->table)->delete()->where('user_id','=',$iUserid)->where('badge_id','=',$badge)->query();
        return true;
    }
    
    //function to get badge details
    public function details($badgeId)
    {
        $result=array('name'=>'','logo'=>'');
        
        if(isset($this->aBadges[$badgeId]))
        {
            $result=$this->aBadges[$badgeId];
        }
        return $result;
    }
    
    //function to get badge logo
    public function getLogo($badge)
    {
        $badge=$this->details($badge);
         
        return (isset($badge['logo'])) ? $badge['logo'] : null;
    }
    
    //function to get a badge name
    public function getName($badge)
    {
        $badge=$this->details($badge);
         
        return (isset($badge['name'])) ? $badge['name'] : null;
        
    }
    
    //functoin to get user badges
    function getUserBadges($iUserid, $limit = null)
    {
        $query = Linko::Database()->table($this->table)
            ->select()->where('user_id','=',$iUserid)->order('time desc');

        if(!empty($limit))
        {
            $query->limit($limit);
        }

        return $query->query()->fetchRows();
    }

    /*
     * Method to get a badge details from the table
     * @param id of the badge
     * @return array
     */
    public function get($iId)
    {
        return Linko::Database()->table('gamification_badge_list')
            ->select()
            ->where('badge_id','=', $iId)
            ->orWhere('badge_ref', '=', $iId)
            ->query()->fetchRow();

    }

    /*
     * Method to get badges for a activity from the badge list table
     * @param activity name like user-login
     * @return array
     */
    public function getActivityBadges($sId)
    {
        return Linko::Database()->table('gamification_badge_list')
                                ->select()
                                ->where('badge_ref','=', $sId)
                                ->order('activity_limit asc')
                                ->query()->fetchRows();

    }

    /*
     * Method to check if badge already exist in badge list
     * @return boolean
     */
    public function exists($badge_ref, $badgename)
    {
        $count = Linko::Database()->table('gamification_badge_list')
                                    ->select()
                                    ->where('badge_ref', '=', $badge_ref)
                                    ->where('badge_name', '=', $badgename)
                                    ->query()->getCount();
        if($count>0) return true;
        return false;
    }

    /*
     * function to to add badges to the badge list
     * @param array of details
     * @return boolean
     */
    public function addBadges($aVals)
    {

        $sBadgeName = $aVals['badge-name'];
        $iBadgeLimit = (isset($aVals['activity-limit'])) ? $aVals['activity-limit'] : 1;
        $oBadgeIcon = null;
        $sBadgeRef = $aVals['badge-ref'];
        $sPoint = (isset($aVals['point'])) ? $aVals['point'] : null;

        if($this->exists($sBadgeRef,$sBadgeName)) return false;

        Linko::Upload()->setAllowedType(array('jpg', 'jpeg', 'png', 'gif'))
            ->setAllowedMime(array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'))
            ->setFilename($sBadgeName)
            ->setMaxSize(5, 'MB')
            ->setOverwrite(true)
            ->setDestination(APP_PATH);

        $sPath ='storage'.DS.'upload'.DS.'badges'.DS;

        //arr::dump($_FILES['image']);

        if(Linko::Upload()->load('image'))
        {
            if(!Dir::exists(APP_PATH . $sPath))
            {
                Dir::create(APP_PATH . $sPath);
            }



            if(Linko::Upload()->save($sPath))
            {
                $sName = Linko::Upload()->getFilename();
                $sExt = Linko::Upload()->getExtension();

                $oBadgeIcon = $sPath . $sName . '_%d.' . $sExt;

                Linko::Image()->load(Linko::Upload()->getFile());

                foreach(array(20, 50, 100, 150, 200) as $iSize)
                {
                    Linko::Image()
                        ->resize($iSize, $iSize, true)
                        ->save(APP_PATH . $sPath . $sName . '_' . $iSize . '.' . $sExt);

                    Linko::Image()->reset();
                }

            }
        }
        if(empty($oBadgeIcon)) return false;

        //let insert to db
        Linko::Database()->table('gamification_badge_list')
                            ->insert(array(
                                'badge_ref' => $sBadgeRef,
                                'badge_name' => $sBadgeName,
                                'badge_icon' => $oBadgeIcon,
                                'activity_limit' => $iBadgeLimit,
                                'time' => time(),
                                'badge_point' => $sPoint
                            ))->query();

        return true;
    }
    

    /*
     * Method to edit a badge
     * @param inputs
     * @param id of the badge to edit
     * @return boolean
     */
    public function editBadge($aVals, $iId)
    {

        $sBadgeName = $aVals['badge-name'];
        $iBadgeLimit = $aVals['badge-limit'];
        $oBadgeIcon = null;
        $sBadgeRef = $aVals['badge-ref'];
        $sPoint = (isset($aVals['point'])) ? $aVals['point'] : null;



        Linko::Upload()->setAllowedType(array('jpg', 'jpeg', 'png', 'gif'))
            ->setAllowedMime(array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'))
            ->setFilename($sBadgeName)
            ->setMaxSize(5, 'MB')
            ->setOverwrite(true)
            ->setDestination(APP_PATH);

        $sPath ='storage'.DS.'upload'.DS.'badges'.DS;

        //arr::dump($_FILES['image']);

        if(Linko::Upload()->load('image'))
        {
            if(!Dir::exists(APP_PATH . $sPath))
            {
                Dir::create(APP_PATH . $sPath);
            }



            if(Linko::Upload()->save($sPath))
            {
                $sName = Linko::Upload()->getFilename();
                $sExt = Linko::Upload()->getExtension();

                $oBadgeIcon = $sPath . $sName . '_%d.' . $sExt;

                Linko::Image()->load(Linko::Upload()->getFile());

                foreach(array(20, 50, 100, 150, 200) as $iSize)
                {
                    Linko::Image()
                        ->resize($iSize, $iSize, true)
                        ->save(APP_PATH . $sPath . $sName . '_' . $iSize . '.' . $sExt);

                    Linko::Image()->reset();
                }

            }
        }


        //let insert to db
        $aUpdate = array(
            'badge_ref' => $sBadgeRef,
            'badge_name' => $sBadgeName,
            'activity_limit' => $iBadgeLimit,
            'badge_point' => $sPoint
        );
        if(!empty($oBadgeIcon)) $aUpdate['badge_icon'] = $oBadgeIcon;
        Linko::Database()->table('gamification_badge_list')
            ->update($aUpdate)
            ->where('badge_id', '=', $iId)
            ->query();

        return true;
    }

    /*
     * Method to delete badge from badge list
     * @parram id
     * @return boolean
     */
    public function delete($iId)
    {
        Linko::Database()->table('gamification_badge_list')
                         ->delete()
                         ->where('badge_id', '=', $iId)->query();
        return true;
    }

}
?>