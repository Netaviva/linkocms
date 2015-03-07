 <?php

/*
*This is the clss use in managing users gamification like adding user delete users
*it provide every method for managing user
*/

class Gamification_Model_Gamification extends Linko_Model
{
    
   public $table='gamification';

    private $aActivities = array();

    //current badge working on details
    public $currentBadge = array();

    //current activity working
    public $currentActivity = null;

    //user working
    public $iUserId = null;

   public function exists($iUserid)
   {
        if(empty($iUserid)) return false;
        $count=Linko::Database()->table($this->table)->select()->where('user_id','=',$iUserid)->query()->fetchRows();
        if(count($count)>0)
        {
            return true;
        }else
        {
            Linko::Model('gamification/action')->add($iUserid);
            return true;
        }
        
   }   
   
   //function to get user gamification details
   public function get($iUserid)
   {

       //for existing of user before let check for existence
        $this->exists($iUserid);
        return Linko::Database()->table($this->table)->select()->where('user_id','=',$iUserid)->query()->fetchRow();
        
   }

    /*
     * Method to list avaliable activities on the site like login
     */
    public function getAvaliableActivities()
    {
        $aActivites = array();

        $aResult = array();
        //when user login
        $this->addActivity(
            array(
                'id'=>'user-login',
                'use-activity'=>1,
                'name' => 'User Login',
                'description' => 'User activity when login to the site',

            ),
            array('name' =>'user', 'method' => 'login','gamify_plugin' => 'user.gamify_login' )
        );


        //when user register
        $this->addActivity(
            array
            (
                'id'=>'user-register',
                'use-activity'=>0,
                'name' => 'User Registration',
                'description' => 'User activity when registering to the site',
                'badge-limit' => 1
            )
            ,array('name' =>'user', 'method' => 'add_user','gamify_plugin' => 'user.gamify_register')
        );

        //adding activitiie for activity module if enabled
        if(Linko::Module()->isModule('activity'))
        {
            $this->addActivity(
                array
                (
                    'id'=>'user-post-status',
                    'use-activity'=>1,
                    'name' => 'User Post Status',
                    'description' => 'User activity with posting of status'
                )
                ,array('name' =>'activity', 'method' => 'add_feed')
            );
        }

        //also for comment activities
        if(Linko::Module()->isModule('comment'))
        {
            $this->addActivity(
                array
                (
                    'id'=>'user-post-comments',
                    'use-activity'=>1,
                    'name' => 'User Post Comments ',
                    'description' => 'User activity with posting comments on different things on site'
                )
                ,array('name' =>'comment', 'method' => 'add_comment')
            );
        }


        //loop through
        foreach($this->aActivities as $key)
        {
            $aResult[$key['id']] = array();
            $aResult[$key['id']]['name'] = $key['name'];
            $aResult[$key['id']]['description'] = $key['description'];
            $aResult[$key['id']]['use-activity'] = $key['use-activity'];
            $aResult[$key['id']]['badges'] = Linko::Model('Gamification/Badge')->getActivityBadges($key['id']);
            $aResult[$key['id']]['plugin'] = $key['plugin'];
            if(isset($key['badge-limit']))
            {
                $aResult[$key['id']]['badge-limit'] = $key['badge-limit'];
            }

        }

        return $aResult;

    }

    /*
     * Method to use by others modules to add activities for admin to use
     * @param array of activity details
     * @param details of plugin to hook to
     * @return this
     */
    public function addActivity($aDetails, $aPlugin = array())
    {

        if(empty($aDetails)) return $this;

        if(!empty($aPlugin))
        {
            $aPlugin['activity-id'] = $aDetails['id'];
            //we are to add a plugin
            $this->addPlugin($aPlugin);
            $aDetails['plugin'] = $aPlugin;
        }

        $this->aActivities[] = $aDetails;

        return $this;
    }

    /*
     * Method to create a plugin on the fly
     * @param array of details
     * @return $this
     */
    public function addPlugin($aDetails)
    {

        if(empty($aDetails)) return $this;
        $sPluginName = $aDetails['name'];
        $sPluginMethod = $aDetails['method'];
        $sActivityId = $aDetails['activity-id'];

         $sFileContent = "
<?php
defined('LINKO') or exit();\n

/**\n
* @author LinkoDEV team\n
* @package linkocms\n
* @subpackage Gamification plugin : ".$sPluginName.".php\n
* @version 1.0.0\n
* @copyright Copyright (c) 2013. All rights reserved.\n
*/\n";

        //file path
        $sFilePath = APP_PATH.'module'.DS.'gamification'.DS.'plugin'.DS.$sPluginName.'.php';

        if(File::exists($sFilePath))
        {

            $sFileOldContent = file_get_contents($sFilePath);
            //we are to check if function exist
            if(!preg_match("#function $sPluginMethod#", $sFileOldContent))
            {

                // append the function to the end
                //remove the end tag
                $sFileOldContent = str_replace('/*endclass*/}', '', $sFileOldContent);


                $sFileOldContent.= "
     public function ".$sPluginMethod."()
     {

         \$args = func_get_args();\n
         Linko::Model('Gamification')->gamify('".$sActivityId."',\$args);\n
    }\n
/*endclass*/}
              ";

                $sFileContent = $sFileOldContent;
                File::write($sFilePath,$sFileContent,null, true);

            }
        }else
        {
            $sFileContent.= "
class Gamification_Plugin_".ucwords($sPluginName)."
     {\n
       public function ".$sPluginMethod."()
       {\n

          \$args = func_get_args();\n
           Linko::Model('Gamification')->gamify('".$sActivityId."',\$args);\n
       }\n

/*endclass*/}
            ";

            //else create the file with the content
            File::write($sFilePath,$sFileContent,null, true);

        }

        return true;

    }


    /*
     * Method to gamify any event on the the site
     * @param activity id
     * @return boolean
     */
    public function gamify($sActivityId,$args = array())
    {


        //let give modules to act for gamifying
        $aActivities = $this->getAvaliableActivities();

        //arr::dump($aActivities);
        if(isset($aActivities[$sActivityId]))
        {

            $aPlugin =  $aActivities[$sActivityId]['plugin'];
            if(isset($aPlugin['gamify_plugin']))
            {

                 Linko::Plugin()->call($aPlugin['gamify_plugin'],$sActivityId,$args);
            }
        }

        $this->iUserId = (empty($this->iUserId)) ? Linko::Model('User/Auth')->getUserId() : $this->iUserId;



        if(empty($this->iUserId)) return false;
        //with this we increament the current activity count for this activity id
        Linko::Model('Gamification/Activity')->increment($sActivityId , $this->iUserId, 1,null);

        $aBadges = Linko::Model('gamification/badge')->getActivityBadges($sActivityId);

        foreach($aBadges as $key => $details)
        {

            $this->currentBadge = $details;

            $limit = $details['activity_limit'];
            $count = Linko::Model('Gamification/Activity')->count($details['badge_ref'] , $this->iUserId, null);


            if($count >= $details['activity_limit'])
            {

                //we award user badge not poccess
                Linko::Model('Gamification/Badge')->add($details['badge_id'],$this->iUserId,function($badge,$iUserId)
                {
                    //boost user level
                    Linko::Model('Gamification/Activity')->increment(Linko::Model('gamification')->currentBadge['badge_ref'],$iUserId,1);

                    echo 'fjgfjgfgjgk';
                    if(Linko::Model('gamification/point')->usePointSystem())
                    {

                        Linko::Model('Gamification/Point')->add($iUserId,Linko::Model('gamification')->currentBadge['badge_point']);
                    }


                    Linko::Plugin()->call('gamification.badgeRecieved',$badge,$iUserId);
                });

            }

        }
    }

    /*
     * function to set userid
     *@param user id
     */
    public function setUserId($iUserId)
    {
        $this->iUserId = $iUserId;
        return $this;
    }
}
?>