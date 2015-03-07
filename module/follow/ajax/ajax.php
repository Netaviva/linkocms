<?php

defined('LINKO') or exit();

class Follow_Ajax extends Linko_Ajax
{
    public function add()
    {
        $iRefId = $this->getParam('refid');

        if(Linko::Model('follow')->isFollowing($iRefId))
        {
            $this->output(Lang::t('follow-error-following'));
        }else
        {
            Linko::Model('follow/action')->add($iRefId);
            $this->output(Lang::t('follow-success-following'));
        }

    }
}