<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage page : model - admincp\admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Page_Model_Admincp extends Linko_Model {

    public function get() {
        $aPages = Linko::Database()->table('page')
                ->select()
                ->query()
                ->fetchRows();

        return $aPages;
    }

    public function getBlockForEdit($iId) {
        return Linko::Database()->table('page_block', 'pb')
                        ->select('pb.block_id, pb.page_id, pb.component_id, pb.block_title, pb.block_position, pb.block_order, mc.*')
                        ->leftJoin('module_component', 'mc', 'mc.component_id = pb.component_id')
                        ->where("block_id = :id")
                        ->query(array(':id' => $iId))
                        ->fetchRow();
    }

}

?>