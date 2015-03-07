<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage page : model - block\block.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Block_Model_Block extends Linko_Model
{
    /**
     * Gets all blocks
     *
     * @param bool $bArrangeByModule should the returned array arranged with module as keys
     * @return array
     */
    public function getBlocks($bArrangeByModule = false)
    {
        $aRows = Linko::Database()->table('module_component', 'mc')
            ->select()
            ->join('module', 'm', 'm.module_id = mc.module_id AND m.enabled = 1')
            ->where("mc.component_type = 'block'")
            ->query()
            ->fetchRows();

        $aBlocks = array();

        if($bArrangeByModule)
        {
            foreach($aRows as $aRow)
            {
                $aBlocks[$aRow['module_id']][] = $aRow;
            }
        }
        else
        {
            $aBlocks = $aRows;
        }

        return $aBlocks;
    }

    /**
     * Gets a block
     *
     * @param int $iId block id
     */
    public function getBlock($iId)
    {
        $aRow = Linko::Database()->table('module_component')
            ->select()
            ->where('component_id', '=', $iId)
            ->where('component_type', '=', 'block')
            ->query()
            ->fetchRow();

        return $aRow;
    }

    /**
     * Gets a block data from the manifest
     *
     */
    public function getBlockManifest($sBlock)
    {
        list($sModule, $sPath) = explode('/', $sBlock, 2);

        $aManifest = Linko::Model('Module')->getManifest($sModule);

        $aBlock = array();
        foreach($aManifest['component']['block'] as $aComponentBlock)
        {
            if($aComponentBlock['path'] == $sBlock)
            {
                $aBlock = $aComponentBlock;

                break;
            }
        }

        if(!isset($aBlock['param']))
        {
            $aBlock['param'] = array();
        }

        if(count($aBlock['param']) && !isset($aBlock['param'][0]))
        {
            $aBlock['param'] = array($aBlock['param']);
        }

        return $aBlock;
    }

    /**
     * Gets a block assigned to a page
     *
     * @param int $iBlockId block id
     */
    public function getPageBlock($iBlockId)
    {
        $aRow = Linko::Database()->table('page_block', 'pb')
            ->select('pb.block_id, pb.page_id, pb.component_id, pb.block_title, pb.block_position, pb.block_order, pb.block_param, pb.dissallow_access, mc.component_id, mc.component_label, mc.component_file, mc.module_id')
            ->leftJoin('module_component', 'mc', 'mc.component_id = pb.component_id')
            ->where('pb.block_id', '=', $iBlockId)
            ->query()
            ->fetchRow();

        $aRow['block_param'] = $aRow['block_param'] != null ? unserialize($aRow['block_param']) : array();
        $aRow['dissallow_access'] = $aRow['dissallow_access'] != null ? unserialize($aRow['dissallow_access']) : array();

        return $aRow;
    }

    /**
     * Gets all blocks assigned to a page
     *
     * @param int $iId page id
     * @return array
     */
    public function getBlocksForPage($iId)
    {
        $aRows = Linko::Database()->table('page_block', 'pb')
            ->select('pb.block_id, pb.page_id, pb.component_id, pb.block_title, pb.block_position, pb.block_order, mc.*')
            ->leftJoin('module_component', 'mc', 'mc.component_id = pb.component_id')
            ->where("page_id = :id")
            ->order('block_order', 'ASC')
            ->query(array(':id' => $iId))
            ->fetchRows();

        $aBlocks = array();

        foreach($aRows as $aRow)
        {
            $aRow['title'] = $aRow['block_title'];

            if($aRow['title'] == "")
            {
                $aRow['title'] = $aRow['component_label'];
            }

            $aBlocks[$aRow['block_position']][] = $aRow;
        }

        return $aBlocks;
    }

	public function getPageBlocks($iPage)
	{
		$aBlocks = array();

		Linko::Cache()->set(array('page', 'blocks_' . $iPage));

		if(!$aBlocks = Linko::Cache()->read())
		{
			$aRows = Linko::Database()->table('page_block', 'pb')
				->select('pb.*, mc.*')
				->join('module_component', 'mc', 'mc.component_id = pb.component_id')
				->where('pb.page_id', '=', $iPage)
				->orWhere('pb.page_id', '=', '0')
				->order('page_id', 'DESC')
				->order('pb.block_order', 'ASC')
				->query()->fetchRows();

			foreach($aRows as $aRow)
			{
				$aBlocks[$aRow['block_position']][] = array_merge($aRow, array(
					'block_param' => $aRow['block_param'] == null ? array() : unserialize($aRow['block_param']),
					'dissallow_access' => $aRow['dissallow_access'] == null ? array() : unserialize($aRow['dissallow_access'])
				));
			}

			Linko::Cache()->write($aBlocks);
		}

		return is_array($aBlocks) ? $aBlocks : array();
	}

    public function cleanup()
    {

    }
}

?>