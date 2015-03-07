<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage blog : entry.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Contest_Controller_View extends Linko_Controller {

    public function main() {
        $sSlug = $this->getParam('slug');

        $aContest = Linko::Model('Contest')->getContestBySlug($sSlug);

        Linko::Plugin()->filter('contest.controller_contest_filter', $aContest);

        if (!count($aContest)) {
            return Linko::Module()->set('_404_', array('message' => 'The page you are looking for does not exists.'));
        }

        $aVals = array();

        $iContestId = (int) $aContest['contest_id'];

        if ($this->getSetting('contest.enable_default_comment') && Linko::Module()->isModule('comment')) {
            Linko::Model('Comment')->init(); // comment
        }

        Linko::Template()->setBreadcrumb(array(
                    'Contest' => Linko::Url()->make('contest:index'),
                    $aContest['contest_slug']))
                ->setTitle($aContest['contest_slug'])
                ->setStyle(array('contest.css'), 'module_contest')
                ->setScript(array('select.js', 'contest.js', 'jquery.countdown.js'), 'module_contest')
                ->setVars(array(
                    'aContest' => $aContest,
                    'iContestId' => $iContestId,
                    'aVals' => $aVals,
                    'bCanComment' => (($this->getSetting('contest.members_only_comment') == true && Linko::Model('User/Auth')->isUser()) || $this->getSetting('contest.members_only_comment') == false)
                        ), $this);

        Linko::Plugin()->call('contest.controller_view_end', $iContestId);
    }

}

?>