<?php

defined('LINKO') or exit();

class Follow_Block_Recent_Follower extends Linko_Controller
{
    public function main()
    {

        $aResult = Linko::Model('follow')->getRecent('follower', $this->getParam('recent_limit_follower'));

        Linko::Template()
            ->setStyle('follow.css', 'module_follow')
            ->setVars(array('aRecentFollower' => $aResult));
    }
}