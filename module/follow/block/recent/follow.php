<?php

defined('LINKO') or exit();

class Follow_Block_Recent_Follow extends Linko_Controller
{
    public function main()
    {

        $aResult = Linko::Model('follow')->getRecent('follow', $this->getParam('recent_limit_follow'));

        Linko::Template()
                        ->setStyle('follow.css', 'module_follow')
                        ->setVars(array('aRecentFollow' => $aResult));
    }
}