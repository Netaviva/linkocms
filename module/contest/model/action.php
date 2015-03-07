<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage blog : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Contest_Model_Action extends Linko_Model {
    /*
     * Method to get a contest details from the table
     * @param id of the badge
     * @return array
     */

    public function get($iId) {
        return Linko::Database()->table('contest')
                        ->select()
                        ->where("contest_id = :id")
                        ->query(array(':id' => $iId))
                        ->fetchRow();
    }

    public function addContest($aVals) {

        $aVals['contest_slug'] = Inflector::slugify($aVals['contest_team_a']) .'-vs-'. Inflector::slugify($aVals['contest_team_b']);
        
        $start_time = date('H:i:s', strtotime($aVals['contest_start_time']));
        $start_date = $aVals['contest_start_date'];
        $final_start_date = date('Y-m-d H:i:s', strtotime($start_date.' '.$start_time));
        
        $end_time = date('H:i:s', strtotime($aVals['contest_end_time']));
        $end_date = $aVals['contest_end_date'];
        $final_end_date = date('Y-m-d H:i:s', strtotime($end_date.' '.$end_time));
        
        $aData = array(
            'contest_slug' => $aVals['contest_slug'],
            'contest_start_date' => strtotime($final_start_date),
            'contest_end_date' => strtotime($final_end_date),
            'contest_team_a' => $aVals['contest_team_a'],
            'contest_team_b' => $aVals['contest_team_b'],
            'is_approved' => isset($aVals['approve']) ? true : false,
        );

        Linko::Upload()->setAllowedType(array('jpg', 'jpeg', 'png', 'gif'))
                ->setAllowedMime(array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'))
                ->setFilename($aVals['contest_slug'])
                ->setMaxSize(5, 'MB')
                ->setOverwrite(true)
                ->setDestination(APP_PATH);
        
       $sPath = date('Y') . '/' . date('m') . '/';
       
       $aLogoPath = array();

        //arr::dump($_FILES['team_logo']);

        $aObjects = Linko::Upload()->load('team_logo');

        if(count($aObjects) <2)
        {
            //arr::dump($aObjects);
            return false;
        }

        //arr::dump($aObjects);
        
       foreach($aObjects as $oUploadObject)
       {
            if(!Dir::exists(DIR_CONTEST_LOGO . $sPath)){
                Dir::create(DIR_CONTEST_LOGO . $sPath);
            }
            $oUploadObject->setOverwrite(true);
            if($oUploadObject->save(DIR_CONTEST_LOGO . $sPath))
            {
                $sName = $oUploadObject->getFilename().time();
                $sExt = $oUploadObject->getExtension();
                
                $sImagePath = $sPath . $sName . '_%d.' . $sExt;

                echo $sImagePath;
                
                $aLogoPath[] = $sImagePath;
                
                Linko::Image()->load($oUploadObject->getFile());

                foreach(array(20,50,60, 75,100, 120, 150, 200) as $iSize)
                {
                   Linko::Image()
                        ->resize($iSize, $iSize, true)
                        ->save(DIR_CONTEST_LOGO . $sPath . $sName . '_' .$iSize . '.' . $sExt);

                    Linko::Image()->reset();
                }
                        

            }
           //arr::dump($oUploadObject->getErrors());
       }

        $aData['contest_team_a_logo'] = $aLogoPath[0];
        $aData['contest_team_b_logo'] = $aLogoPath[1];

        $iContestId = Linko::Database()->table('contest')
                ->insert($aData)
                ->query()
                ->getInsertId();

        Linko::Plugin()->call('contest.create_contest', $iContestId, $aVals);


        Linko::Cache()->delete('contest', 'dir');

        return true;
    }
    
    public function editContest($aVals, $iId) {

        $aVals['contest_slug'] = Inflector::slugify($aVals['contest_team_a']) .'-vs-'. Inflector::slugify($aVals['contest_team_b']);
        
        $start_time = date('H:i:s', strtotime($aVals['contest_start_time']));
        $start_date = $aVals['contest_start_date'];
        $final_start_date = date('Y-m-d H:i:s', strtotime($start_date.' '.$start_time));
        
        $end_time = date('H:i:s', strtotime($aVals['contest_end_time']));
        $end_date = $aVals['contest_end_date'];
        $final_end_date = date('Y-m-d H:i:s', strtotime($end_date.' '.$end_time));
        
        $aUpdate = array(
            'contest_slug' => $aVals['contest_slug'],
            'contest_start_date' => strtotime($final_start_date),
            'contest_end_date' => strtotime($final_end_date),
            'contest_team_a' => $aVals['contest_team_a'],
            'contest_team_b' => $aVals['contest_team_b'],
            'is_approved' => isset($aVals['approve']) ? true : false,
        );
        
              Linko::Upload()->setAllowedType(array('jpg', 'jpeg', 'png', 'gif'))
                ->setAllowedMime(array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'))
                ->setFilename($aVals['contest_slug'])
                ->setMaxSize(5, 'MB')
                ->setOverwrite(true)
                ->setDestination(APP_PATH);
        
       $sPath = date('Y') . '/' . date('m') . '/';
       
       $aLogoPath = array();
        
       foreach(Linko::Upload()->load('team_logo') as $oUploadObject){

            if(!Dir::exists(DIR_CONTEST_LOGO . $sPath))
            {
                Dir::create(DIR_CONTEST_LOGO . $sPath);
            }  
            if($oUploadObject->save(DIR_CONTEST_LOGO . $sPath))
            {
                $sName = $oUploadObject->getFilename();
                $sExt = $oUploadObject->getExtension();
                
                $sImagePath = $sPath . $sName . '_%d.' . $sExt;

                if(empty($aLogoPath))
                {
                    $aLogoPath['a'] = $sImagePath;
                }
                elseif(count($aLogoPath)>0)
                {
                    $aLogoPath['b'] = $sImagePath;
                }

                
                Linko::Image()->load($oUploadObject->getFile());

                foreach(array(20,50,60, 75,100, 120, 150, 200) as $iSize)
                {
                   Linko::Image()
                        ->resize($iSize, $iSize, true)
                        ->save(DIR_CONTEST_LOGO . $sPath . $sName . '_' .$iSize . '.' . $sExt);

                    Linko::Image()->reset();
                }
                
            }
       }

        //arr::dump($aLogoPath);
        //exit;
        if(isset($aLogoPath['a']))
        {
            $aUpdate['contest_team_a_logo'] = $aLogoPath['a'];
        }

        if(isset($aLogoPath['b']))
        {
            $aUpdate['contest_team_b_logo'] = $aLogoPath['b'];
        }


        
        Linko::Database()->table('contest')
                ->update($aUpdate)
                 ->where("contest_id = :id")
                ->query(array(':id' => $iId));

        Linko::Cache()->delete('contest', 'dir');

        return true;
    }

    public function deleteContest($iId) {
        Linko::Database()->table('contest')
                ->delete()
                ->where("contest_id = :id")
                ->query(array(':id' => $iId));

        Linko::Plugin()->call('contest.delete_contest', $iId);

        Linko::Cache()->delete('contest', 'dir');

        return true;
    }

    public function approveContest($mId) {
        if (!is_array($mId)) {
            $mId = array($mid);
        }

        if (!count($mId)) {
            return true;
        }

        Linko::Database()->table('contest')
                ->update(array(
                    'is_approved' => true
                ))
                ->whereIn('contest_id', $mId)
                ->query();

        Linko::Plugin()->call('contest.approve_contest', $mId);

        Linko::Cache()->delete('contest', 'dir');

        return true;
    }

    public function unapproveContest($mId) {
        if (!is_array($mId)) {
            $mId = array($mid);
        }

        if (!count($mId)) {
            return false;
        }

        Linko::Database()->table('contest')
                ->update(array(
                    'is_approved' => false
                ))
                ->whereIn('contest_id', $mId)
                ->query();

        Linko::Plugin()->call('contest.unapprove_contest', $mId);

        Linko::Cache()->delete('contest', 'dir');

        return true;
    }

}

?>