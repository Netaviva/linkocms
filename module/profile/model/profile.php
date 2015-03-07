<?php

defined('LINKO') or exit();

class Profile_Model_Profile extends Linko_Model
{
    //profile owner id
    public $iOwnerId;

    //profile viewer id
    public $iViewerId = 0;

    //owners profile details
    private $_aOwnerDetails = array();

    //viewer profile details
    private $_aViewerDetails = array();

    private $_menus=array();

    private $_defaultProfileSlug = 'info';

    //profile stats showin on the profile toosl
    private $_aStats = array();



    public function  __construct()
    {
    }

    /*
     * method to get profile owner
     * @return int
     */
    public function getOwnerId()
    {
        return $this->iOwnerId;

    }


    /*
     * Nethod to set profile details like owner,viewer and there details
     * @param $user-is username of profile owner
     * @return this object
     */
    public function set($sUser)
    {
        //set owwner details
        $this->_aOwnerDetails = Linko::Model('user')->getUser($sUser);

        if(Linko::Model('User/Auth')->isUser())
        {
            $this->_aViewerDetails = Linko::Model('User')->getUser(Linko::Model('User/Auth')->getUserBy('username'));
        }
        //arr::dump($this->_aViewerDetails);
        //if user doesnot exist we are to show error 404
        if(!$this->exists()) return false;

        $this->iOwnerId = $this->_aOwnerDetails['user_id'];
        // check if the activitiy module is activated
        if(Linko::Module()->isModule('activity'))
        {
            //register menu
            $this->registerMenu(Lang::t('profile-activity-title'), array('link' => $this->buildUrl(),'id' => 'activity'));
        }

        $sModule = Linko::Module()->getSetting('profile.default_profile_module');



        if(!empty($sModule) && $sModule != 'info')
        {

            if(Linko::Module()->hasTask($sModule, 'profile_default_slug'))
            {

               $this->_defaultProfileSlug = Linko::Module()->callTask($sModule, 'profile_default_slug');

            }

        }

        if($sModule == 'activity')
        {
            $this->_defaultProfileSlug = 'activity';
        }


        if(Linko::Module()->isModule('activity'))
        {
            Linko::Router()->add('activity(/[page])' , array(
                'id' => 'profile:activity',
                'controller' => 'activity/index',
                'rules' => array
                (
                    'page' => ':int'
                )
            ));
        }

        //now set the
        //auto register default route like the info own
        Linko::Router()->add('info' , array(
            'id' => 'profile:info',
            'controller' => 'profile/info',

        ));
        //also auto register menus

        $this->registerMenu(Lang::t('profile.basic-info'), array('link' => $this->buildUrl('info'),'id' => 'info'));
        //$this->registerMenu('Activities', array('link' => $this->buildUrl('info'),'id' => 'infodsss'));

        Linko::Plugin()->call('profile.init');

        return $this;
    }

    //method to check if user exists
    public function exists()
    {
        if(empty($this->_aOwnerDetails) || !isset($this->_aOwnerDetails['user_id']))
        {
            return false;
        }
        return true;

    }

    public function buildUrl($url = null)
    {
        return Linko::Url()->make('user:profile', array('username' => $this->get('username'), 'slug' => $url));
    }
    /*
     * Method to register profile menues
     * @param name of the menu
     * @param array of menu details like link and others
     * @return $this
     */
    public  function registerMenu($sName , $aParam)
    {
        if(empty($sName) || empty($aParam)) return $this;

        $this->_menus[$sName] = $aParam;

        return $this;
    }

    /*
     * methood to get added menus
     *
     */
    public  function getMenus()
    {
        return $this->_menus;
    }
    /*
     * Method to set current controller
     * @return true
     */
    public function setController($sSlug)
    {
       //s echo $sSlug;
        $sSlug = (empty($sSlug)) ? $this->_defaultProfileSlug : $sSlug;

        $oRoute = Linko::Router()->route($sSlug);

        if($oRoute->controller != '_404_')
        {

            Linko::Module()->set($oRoute->controller, array_merge($oRoute->args, array('user_id' => $this->getOwnerId())));
        }
        else
        {

        }

    }

    /*
     * Method to get any thing from owner or viewer details
     * @param index index to get user details like name,id,username
     * @param type type of dfetails to get owner or viewer details
     * @return string
     */
    public function get($sIndex = null, $type = "owner")
    {
        if(empty($sIndex)) return $this->_aOwnerDetails;
        $aDetails = ($type == 'viewer') ? $this->_aViewerDetails : $this->_aOwnerDetails;

        if(isset($aDetails[$sIndex])) return $aDetails[$sIndex];

        return null;

    }

    /*
     * method to check if viewer is a guest
     * @return boolean
     */
    public function isViewerGuest()
    {
        if(empty($this->_aViewerDetails)) return true;

        return false;
    }

    public function getBasicInfo()
    {
        $aDetails = array
        (
            Lang::t('user.username') => $this->get('username'),
            Lang::t('user.fullname') => $this->get('lastname').' '. $this->get('firstname'),

        );
        if(Linko::Module()->getSetting('display_email_on_profile'))
        {
            $aDetails['user.email'] = $this->get('email');
        }

       // $aDetails['Language'] = $this->get('language_title');
        $aDetails['Joined'] = Date::getTime(Linko::Module()->getSetting('date.time_format'), $this->get('time_joined'));

        return $aDetails;
    }

    /*
     * method to add profile stats
     * @param array of stats details array('name'=>'activities,'number'=>4)
     * @return $this
     */
    public function addStatistic($aDetails)
    {

        if(empty($aDetails)) return $this;

        $this->_aStats[] = $aDetails;


        return $this;
    }

    /*
     * method to get profile stats
     * @return array
     */
    public function getStatistic()
    {
        return $this->_aStats;

    }

    /*
     * Method to use by other modules to know if user is on profile page
     * @return boolean
     */
    public function isProfile()
    {
        if(!empty($this->_aOwnerDetails)) return true;
        return false;
    }


}
