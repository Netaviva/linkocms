<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage comment : block - display.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Comment_Block_Display extends Linko_Controller
{
    public function main()
    {
        $sModule = $this->getParam('module_id');
        
        $iItemId = $this->getParam('item_id');
        
        list($aComments, $iTotal) = Linko::Model('Comment')->getComments($iItemId, $sModule);
        
        Linko::Template()->setVars(array(
            'aComments' => $aComments,
        ), $this);
    }
}

?>