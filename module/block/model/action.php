<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage page : model - block\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Block_Model_Action extends Linko_Model
{
    // assign a block
    public function assignBlock($iPage, $aVals)
    {
        // component_id, title, position, param:optional, dissallow_access:optional
        $aParams = isset($aVals['param']) ? serialize($aVals['param']) : NULL;

        if(!isset($aVals['dissallow_access']))
        {
            $aVals['dissallow_access'] = array();
        }

        $iBlockId = Linko::Database()->table('page_block')
            ->insert(array(
                'page_id' => $iPage,
                'component_id' => $aVals['component_id'],
                'block_position' => $aVals['position'],
                'block_title' => $aVals['title'],
                'block_order' => 0, // @todo update this
                'block_param' => $aParams,
                'dissallow_access' => serialize($aVals['dissallow_access'])
            ))
            ->query()
            ->getInsertId();

        return $iBlockId;
    }

    /**
     * Updates a block
     *
     * @param int $iId block id
     * @param array $aVals key/values
     * @return bool
     */
    public function updateBlock($iId, $aVals)
    {
        $aData = array();

        $aData['block_title'] = $aVals['title'];

        if(isset($aVals['position']))
        {
            $aData['block_position'] = $aVals['position'];
        }

        if(isset($aVals['param']))
        {
            $aData['block_param'] = serialize($aVals['param']);
        }

        if(isset($aVals['dissallow_access']))
        {
            $aData['dissallow_access'] = serialize($aVals['dissallow_access']);
        }


        Linko::Database()->table('page_block')
            ->update($aData)
            ->where('block_id', '=', $iId)
            ->query();

        return true;
    }

    // delete a block
    public function deleteBlock($iId)
    {
        Linko::Database()->table('page_block')
            ->delete()
            ->where('block_id', '=', $iId)
            ->query();

        return true;
    }

    public function updateBlockOrder($aOrder)
    {
        foreach($aOrder as $iId => $iOrder)
        {
            $iOrder = (int)$iOrder;

            Linko::Database()->table('page_block')
                ->update(array('block_order' => $iOrder))
                ->where('block_id', '=', $iId)
                ->query();
        }

        return true;
    }

    public function deleteModuleBlocks($sModule)
    {
        Linko::Database()->table('page_block')
            ->delete()
            ->whereIn('component_id', (
                Linko::Database()->table('module_component')
                    ->select('component_id')
                    ->where('module_id', '=', $sModule)
                ))
            ->query();

        return true;
    }
}