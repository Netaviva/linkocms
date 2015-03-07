<?php

class Activity_Controller_View extends Linko_Controller
{
    public function main()
    {
        if(!Linko::Model('User/Auth')->isUser() && $this->getSetting('activity.allow_guest') === false)
        {
            return Linko::Module()->set('_404_', array(
                'message' => Lang::t('activity.you_cannot_view_activities')
            ));
        }

	    $iActivityId = (int)$this->getParam('id');

	    $aActivity = Linko::Model('Activity')->getForDisplay($iActivityId);

	    if(!count($aActivity))
	    {
		    return Linko::Module()->set('_404_', array(
			    'message' => Lang::t('activity.invalid_activity')
		    ));
	    }

        Linko::Template()
            ->setScript('activity.js','module_activity')
            ->setStyle('activity.css','module_activity')
            ->setBreadcrumb(array(

            ), 'Activity #' . $iActivityId )
            ->setTitle('Activity #' . $iActivityId )
            ->setHeader(array(
                Html::script('var activity_live_timeupdate = ' . ($this->getSetting('activity.live_timer_count') ? 'true' : 'false') . ';'
            )), 60)
            ->setVars(array(
				'aActivity' => $aActivity,
	            'bCommentEnabled' => Linko::Model('Activity')->commentEnabled(),
	            'bCanComment' => Linko::Model('Activity')->canComment(),
            ))
	        ->setTranslation(array(
		        'activity.no_more_activity',
		        'activity.comment_added'
            ));
    }
}