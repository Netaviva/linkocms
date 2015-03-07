<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage page : ajax - ajax.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Block_Ajax extends Linko_Ajax
{
    public function updateBlockOrder()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->toJson(array());
        }

        $aBlockOrder = $this->getParam('order');
        
        Linko::Model('Block/Action')->updateBlockOrder($aBlockOrder);
        
        $this->toJson($aBlockOrder);
    }

    public function getBlockForm()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->toJson(array());
        }

        $this->output(Linko::Module()->getBlock('block/admincp/block-form', array(
            'component_id' =>  $this->getParam('component_id'),
            'block_id' => $this->getParam('block_id')
        )), 'html');
    }

    public function deleteBlock()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->toJson(array());
        }

        $iBlockId = $this->getParam('block_id');

        if(Linko::Model('Block/Action')->deleteBlock($iBlockId))
        {
            return $this->toJson(array(
                'success' => true,
                'message' => 'Block Deleted!'
            ));
        }
    }

    public function assignBlock()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->toJson(array());
        }

        $sPosition = $this->getParam('position');

        parse_str($this->getParam('form'), $aPost);

        if(count($aPost))
        {
            Linko::Validate()->set(array(
                /**'title' => array(
                    'function' => 'required',
                    'error' => 'You must enter a title'
                )/**/
            ));

            if(Linko::Validate()->isValid($aPost))
            {
                $sTitle = $aPost['title'];
                $aParam = isset($aPost['param']) ? $aPost['param'] : array();
                $iComponentId = $this->getParam('component_id');
                $iPageId = $this->getParam('page_id');

                if(!isset($aPost['dissallow_access']))
                {
                    $aPost['dissallow_access'] = NULL;
                }

                if($iPageBlockId = Linko::Model('Block/Action')->assignBlock($iPageId, array(
                    'component_id' => $iComponentId,
                    'title' => $sTitle,
                    'position' => $sPosition,
                    'param' => $aParam,
                    'dissallow_access' => $aPost['dissallow_access']
                )))
                {
                    $aBlock = Linko::Model('Block')->getPageBlock($iPageBlockId);

                    return $this->toJson(array(
                        'success' => true,
                        'message' => 'Block assigned',
                        'block' => $aBlock
                    ));
                }
            }
            else
            {
                return $this->toJson(array(
                    'error' => true,
                    'message' => current(Linko::Error()->get())
                ));
            }
        }
    }

    public function updateBlock()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->toJson(array());
        }

        parse_str($this->getParam('form'), $aPost);

        if(count($aPost))
        {
            Linko::Validate()->set(array(
                /**'title' => array(
                    'function' => 'required',
                    'error' => 'You must enter a title'
                )/**/
            ));

            if(Linko::Validate()->isValid($aPost))
            {
                $sTitle = $aPost['title'];
                $aParam = isset($aPost['param']) ? $aPost['param'] : array();
                $iBlockId = $this->getParam('block_id');

                if(!isset($aPost['dissallow_access']))
                {
                    $aPost['dissallow_access'] = NULL;
                }

                if(Linko::Model('Block/Action')->updateBlock($iBlockId, array(
                    'title' => $sTitle,
                    'param' => $aParam,
                    'dissallow_access' => $aPost['dissallow_access']
                )))
                {
                    $aBlock = Linko::Model('Block')->getPageBlock($iBlockId);

                    return $this->toJson(array(
                        'success' => true,
                        'message' => 'Block updated',
                        'block' => $aBlock
                    ));
                }
            }
            else
            {
                return $this->toJson(array(
                    'error' => true,
                    'message' => current(Linko::Error()->get())
                ));
            }
        }
    }
}