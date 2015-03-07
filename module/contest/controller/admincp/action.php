<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage contest : admincp\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Contest_Controller_Admincp_Action extends Linko_Controller {

    public function main() {
        $aVals = array();
        $aDetails = array();
        $iId = $this->getParam('id');
        $sAction = $this->getParam('action');
        $bEdit = false;

        Linko::Template()->setBreadcrumb(array(
                    Lang::t('contest.sport') => Linko::Url()->make('contest.admincp'),
                        ), Lang::t('contest.sport'))
                ->setStyle(array('admin.css', 'contest.css'), 'module_contest')
                ->setScript(array('select.js', 'contest.js', 'jquery.timepicker.js'), 'module_contest');

        Linko::Validate()->set('create_contest', array(
            'contest_start_date' => array('function' => 'required', 'error' => 'Please set a date for the contest to start'),
            'contest_start_time' => array('function' => 'required', 'error' => 'The time for contest to start is required'),
            'contest_end_date' => array('function' => 'required', 'error' => 'Please set a date for the contest end'),
            'contest_end_time' => array('function' => 'required', 'error' => 'The time for contest to end is required'),
        ));

        switch ($sAction) {
            case 'add':
                if ($aVals = Input::post('val')) {
                    if (Linko::Validate()->isValid($aVals)) {
                        if (Linko::Model('Contest/Action')->addContest($aVals)) {
                            Linko::Flash()->success(Lang::t('contest.contest_added_successfully'));
                            Linko::Response()->redirect('contest:admincp');
                        }
                        else
                        {
                            Linko::Flash()->error(Lang::t('contest.failed_contest_added'));
                        }
                    }
                }
                Linko::Template()->setBreadcrumb(array('Create'), Lang::t('contest.create'))
                        ->setTitle(Lang::t('contest.create'));
                break;

            case 'edit':
                $aDetails = Linko::Model('Contest/Action')->get($iId);
                //Get the contest start date and time
                $contest_start_date = date('F j, Y', $aDetails['contest_start_date']);
                $contest_start_time = date('g:ia', $aDetails['contest_start_date']);
                //Get the contest end date and time
                $contest_end_date = date('F j, Y', $aDetails['contest_end_date']);
                $contest_end_time = date('g:ia', $aDetails['contest_end_date']);

                $bEdit = true;
                if ($aVals = Input::post('val')) {
                    if (Linko::Validate()->isValid($aVals)) {
                        if (Linko::Model('contest/action')->editContest($aVals, $iId)) {
                            Linko::Flash()->success(Lang::t('contest.contest_edited_successfully'));
                            Linko::Response()->redirect('self');
                        }
                    }
                }
                Linko::Template()->setVars(array(
                    'contest_start_date' => $contest_start_date,
                    'contest_start_time' => $contest_start_time,
                    'contest_end_date' => $contest_end_date,
                    'contest_end_time' => $contest_end_time,
                    'team_a_logo' => $aDetails['contest_team_a_logo'],
                    'team_b_logo' => $aDetails['contest_team_b_logo']
                ));
                break;

            case 'delete':
                Linko::Model('Contest/Action')->deleteContest($iId);
                Linko::Flash()->success("Contest deleted successfully.");
                Linko::Response()->redirect('contest:admincp');
                break;
        }

        Linko::Template()->setVars(array(
            'aVals' => $aVals,
            'bEdit' => $bEdit,
            'aDetails' => $aDetails,
        ));
    }

}

?>