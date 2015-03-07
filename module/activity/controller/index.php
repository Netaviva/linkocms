<?php

class Activity_Controller_Index extends Linko_Controller
{
    public function main()
    {
        if(!Linko::Model('User/Auth')->isUser() && $this->getSetting('activity.allow_guest') === false)
        {
            return Linko::Module()->set('_404_', array(
                'message' => Lang::t('activity.you_cannot_view_activities')
            ));
        }

        $iUser = $this->getParam('user_id');

        Linko::Template()
            ->setScript('activity.js','module_activity')
            ->setStyle('activity.css','module_activity')
            ->setHeader(array(
                Html::script('var activity_live_timeupdate = ' . ($this->getSetting('activity.live_timer_count') ? 'true' : 'false') . ';'
            )), 60)
            ->setTranslation(array(
	            'activity.no_more_activity',
	            'activity.comment_added'
            ))
            ->setVars(array(
                'iUser' => $iUser
            ));
    }
}