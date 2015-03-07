<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : model - browse.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class User_Model_Browse extends Linko_Model
{
    private $_oQuery;
    
    private $_bOnline = false;
    
    private $_bVerified = true;
    
    public function __construct()
    {
        
    }
    
    public function online($bIsOnline)
    {
		$this->_bIsOnline = $bIsOnline;
		
		return $this;       
    }

    public function verified($bVerified)
    {
		$this->_bVerified = $bVerified;
		
		return $this;       
    }
        
    public function sort($sField, $sOrder = 'DESC')
    {
		$this->_aOrder = array($sField, $sOrder);
		
		return $this;        
    }
    
    public function get($iPage = 1, $iLimit = 5)
    {
        $oQuery = Linko::Database()->table('user', 'u')
            ->select(Linko::Model('User')->getTableFields('u', 'ud').', ur.role_id, ur.role_title')
            ->leftJoin('user_data', 'ud', 'ud.user_id = u.user_id')
            ->leftJoin('user_role', 'ur', 'ur.role_id = u.role_id');
        
        if($this->_bVerified)
        {
            $oQuery->where('activated', '!=', 0);
        }
        
        list($iTotal, $aRows) = $oQuery
            ->group('u.user_id')
            ->query()
            ->paginate($iPage, $iLimit);

        foreach($aRows as $iKey => $aRow)
        {

        }
        
        return array($iTotal, $aRows);    
    }
}

?>