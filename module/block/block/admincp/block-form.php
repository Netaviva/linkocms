<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage page : block - admincp\block-form.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Block_Block_Admincp_Block_Form extends Linko_Controller
{
    public function main()
    {
        $sPosition = $this->getParam('position');
        $iComponentId = (int)$this->getParam('component_id');
        $iBlockId = (int)$this->getParam('block_id');
        $bEdit = false;

        if($iBlockId)
        {
            $bEdit = true;
            $aComponent = Linko::Model('Block')->getPageBlock($iBlockId);
        }
        else
        {
            $aComponent = Linko::Model('Block')->getBlock($iComponentId);
            $aComponent['block_param'] = array();
        }

        $aBlockManifest = Linko::Model('Block')->getBlockManifest($aComponent['component_file']);

        Linko::Template()->setVars(array(
            'aUserRoles' => Linko::Model('User/Role')->getRoles(),
            'aComponent' => $aComponent,
            'aParam' => $aBlockManifest['param'],
            'bEdit' => $bEdit
        ));
    }
}

?>