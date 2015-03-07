<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage contentblock : model - contentblock.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Contentblock_Model_Contentblock extends Linko_Model
{
	public function get($iId = null)
	{
        Linko::Cache()->set(array('contentblock', 'block' . ($iId ? '_' . $iId : null)));
        
        if(!$aRows = Linko::Cache()->read())
        {
    		$oDatabase = Linko::Database()->table('contentblock')
                ->select();
                
    		if($iId)
    		{
                $aRows = $oDatabase->where('contentblock_id', '=', $iId)
                    ->query()
                    ->fetchRow();
    		}
            else
            {
                $aRows = $oDatabase->query()
                    ->fetchRows();
            }
            
            Linko::Cache()->write($aRows);            
        }
		
		return $aRows;
	}

    public function getBlocksHelper()
    {
        $aRows = $this->get();
        $aBlocks = array();

        foreach($aRows as $aRow)
        {
            $aBlocks[$aRow['contentblock_id']] = $aRow['title'];
        }

        return $aBlocks;
    }
}

?>