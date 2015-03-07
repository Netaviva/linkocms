<?php
defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage contest : model - contest.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

class Contest_Model_Contest extends Linko_Model {
     /**
     * Gets a single Contest post
     *
     * @param mixed   $mId    contest id or contest url slug
     * @param boolean $bParse uses shortcode to parse content if set to true
     * @param bool    $bOverrideApprove
     *
     * @return array
     */
    public function getContest($mId, $bParse = true, $bOverrideApprove = false)
    {
        Linko::Cache()->set(array('contest', 'post_' . $mId . '_' . ($bParse ? 'parsed' : 'original')));
        
        if(!$aContest = Linko::Cache()->read())
        {
            $sCond = (is_numeric($mId) ? "ct.contest_id = :mid" : "ct.contest_slug = :mid");
            
      		$oQuery = Linko::Database()->table('contest', 'ct')
                ->select("ct.*")
                ->where($sCond);
            
            if(!$bOverrideApprove)
            {
                $oQuery->where("ct.is_approved", '=', true);
            }  
            
            $aContest = $oQuery->query(array(':mid' => $mId))->fetchRow();

	        if(!isset($aContest['contest_id']))
	        {
				return array();
	        }

            $this->prepareContest($aContest, $bParse);

            Linko::Cache()->write($aContest);
        }
        
        return $aContest;
    }
 
        /**
	 * See getContest()
	 * 
	 */	   
	public function getContestBySlug($sSlug, $bParse = true, $bOverrideApprove = false)
	{		
        return $this->getContest((string)$sSlug, $bParse, $bOverrideApprove);	
	}

	/**
	 * See getContest()
	 * 
	 */	
	public function getContestById($iId, $bParse = true, $bOverrideApprove = false)
	{		
        return $this->getContest((int)$iId, $bParse, $bOverrideApprove);	
	}
    	

    //function to check if user has contest or nor
    function hasParticipated($iContestId, $iUserId = null) {

        $iUserId = (empty($iUserId)) ? Linko::Model('User/Auth')->getUserId() : $iUserId;

        $iId = Linko::Database()->table('contestant')
                ->select()
                ->where("user_id = '" . $iUserId . "' AND contest_id = '" . $iContestId . "'")
                ->query()
                ->fetchRows();

        if (count($iId) > 0) {
            return true;
        } else {
            return false;
        }
    }

    //function to off visibility of things about contest
    public function offVisible($id) {
        Linko::Database()->table('contest')->update(array('visible' => 0))->where('id', '=', $id)->query();
        return true;
    }

    public function onVisible($id) {
        Linko::Database()->table('contest')->update(array('visible' => 1))->where('id', '=', $id)->query();
        return true;
    }
    
     /**
     * Checks to see if comment is possible.
     *
     * @return boolean
     */
    public function isCommentEnabled()
    {
        return (bool)(Linko::Module()->getSetting('contest.enable_default_comment') && Linko::Module()->isModule('comment'));
    }


    public function prepareContest(&$aRow, $bParse) {
        $sDateFormat = Linko::Config()->get('date.format');
        $aRow['contest_start_date'] = Date::getTime($sDateFormat, $aRow['contest_start_date']);
        $aRow['contest_end_date'] = Date::getTime($sDateFormat, $aRow['contest_end_date']);
        $aRow['contest_url'] = Linko::Url()->make('contest:view', array('slug' => $aRow['contest_slug']));
        $aRow['total_comments'] = $this->isCommentEnabled() ? count(Linko::Model('Comment')->getComments($aRow['contest_id'], 'contest')) : 0;
        
        return $aRow;
    }

}

?>