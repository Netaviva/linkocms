<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage contest : model - browse.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Contest_Model_Browse extends Linko_Model
{
    private $_bApproved = true;
    
    private $_sArchive;
        
    private $_mId;
    
    private $_iLimit;
    
    private $_iPage;
    
    public function __construct()
    {
        
    }
    
    public function archive($sArchive = null, $mId = null)
    {
        $this->_sArchive = $sArchive;
        
        $this->_mId = $mId;
        
        return $this;
    }

    public function approved($bApproved)
    {
        $this->_bApproved = $bApproved;
        
        return $this;
    }

    public function limit($iLimit)
    {
        $this->_iLimit = $iLimit;
        
        return $this;
    }

    public function page($iPage)
    {
        $this->_iPage = $iPage;
        
        return $this;
    }
              
    public function get()
    {
        $sCache = Linko::Cache()->set(array('contest', 'browse_' . md5($this->_mId . $this->_iLimit . $this->_iPage . $this->_bApproved)));
        
        if(!$aData = Linko::Cache()->read($sCache))
        {
            $oBrowse = Linko::Database()->table('contest', 'ct')           
                ->select('ct.*');
            
            if($this->_bApproved)
            {
                $oBrowse->where("ct.is_approved = '" . true . "' AND ct.contest_end_date > '". time() ."'");
            }

            Linko::Plugin()->filter('contest.model_browse_contests_filter', $oBrowse);
            
            list($iTotal, $aRows) = $oBrowse->order('ct.contest_end_date', 'ASC')
                ->group('ct.contest_id')
                ->query()
                ->paginate($this->_iPage, $this->_iLimit);
            
            foreach($aRows as $iKey => $aRow)
            {
                Linko::Model('Contest')->prepareContest($aRow, false);

                $aRows[$iKey] = $aRow;
 
            }
            
            $aData = array($iTotal, $aRows);
            
            Linko::Cache()->write($aData, $sCache); 
        }
        
        return $aData;
    }
}

?>