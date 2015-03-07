<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage contentblock : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Contentblock_Model_Action extends Linko_Model
{
	public function add($aVals, $iId = null, $iBlockId = null)
	{
        $oDb = Linko::Database();
        
		$bEdit = $iId ? true : false;
		
		$aData = array(
			'title' => $aVals['title'],
			'text' => $aVals['text']
		);
		
		if($bEdit)
		{
            // Update contentblock table
			$oDb->table('contentblock')
				->update($aData)
				->where("contentblock_id = :cid")
				->query(array(':cid' => $iId));

            Linko::Plugin()->call('contentblock.update', $iBlockId);

			return true;
		}
		else
		{
			$aData['time_created'] = time();
			
			// Insert contentblock table
			$iBlockId = Linko::Database()->table('contentblock')
				->insert($aData)
				->query()
				->getInsertId();

            Linko::Plugin()->call('contentblock.add', $iBlockId);
		
			return true;	
		}
	}

    public function delete($iId)
    {
        Linko::Database()->table('contentblock')
            ->delete()
            ->where('contentblock_id', '=', $iId)
            ->query();

        return true;
    }
}

?>