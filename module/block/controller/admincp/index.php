<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage page : admincp\block.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Block_Controller_Admincp_Index extends Linko_Controller
{
    public function main()
    {
        // Clean up modules
        Linko::Model('Module')->cleanup();

        $iPageId = (($iPageId = (int)$this->getParam('page_id')) && $iPageId != null) ? $iPageId : 0;
        $aBlocks = Linko::Model('Block')->getBlocks(false);
        $aPageBlocks = Linko::Model('Block')->getBlocksForPage((int)$iPageId);
        $aPages = Linko::Model('Page')->getPages();
        $aPage = array();
        $sLayout = 'template';

        if($iPageId)
        {
            $aPage = Linko::Model('Page')->getPage($iPageId);
            $sLayout = isset($aPage['page_layout']) && $aPage['page_layout'] ? $aPage['page_layout'] : $sLayout;
        }

        $aPositions = Linko::Model('Theme')->getPositions($sLayout);

        Linko::Template()->setScript(array('block.js'), 'module_block')
            ->setStyle(array('block.css'), 'module_block')
            ->setTitle('Block Manager')
            ->setBreadcrumb(array(), 'Block Manager')
            ->setVars(array(
                'aBlocks' => $aBlocks,
                'aPages' => $aPages,
                'aPage' => $aPage,
                'iPageId' => $iPageId,
                'aPositions' => $aPositions,
                'aPageBlocks' => $aPageBlocks,
            ));
    }
}

?>