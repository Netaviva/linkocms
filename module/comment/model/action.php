<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage comment : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Comment_Model_Action extends Linko_Model
{
    public function addComment($aVals, $iId = null)
    {
        /**
         * item_id, module_id, comment
         */
        $bUpdate = false;
        
        $sModule = $aVals['module_id'];
        $iItemId = $aVals['item_id'];
        
        if($iId)
        {
            $bUpdate = true;
        }
        
        $aData = array(
            'comment_text' => $aVals['comment'],
            'author_name' => isset($aVals['name']) ? $aVals['name'] : null,
            'author_email' => isset($aVals['email']) ? $aVals['email'] : null
        );
        
        if($bUpdate)
        {
            $aData['time_updated'] = Date::now();
            
            Linko::Database()->table('comment')
                ->insert($aData)
                ->where('module_id', '=', $sModule)
                ->where('comment_id', '=', $iId)
                ->query();
              
            Linko::Plugin()->call('comment.update_comment', $iId, $aVals, $sModule, $iItemId);
              
            return $iId;
        }
        else
        {
            $aData['module_id'] = $sModule;
            $aData['item_id'] = (int)$aVals['item_id'];
            $aData['parent_comment_id'] = (int)(isset($aVals['parent_id']) ? $aVals['parent_id'] : 0);
            $aData['user_id'] = Linko::Model('User/Auth')->getUserId();
            $aData['time_created'] = Date::now();
            $aData['time_updated'] = Date::now();
            
            $iInsertId = Linko::Database()->table('comment')
                ->insert($aData)
                ->query()
                ->getInsertId();
              
            Linko::Plugin()->call('comment.add_comment', $iInsertId, $aVals, $sModule, $iItemId);
              
            return $iInsertId;
        }
    }
    
    /**
     * Updates a comment
     * 
     * @param int $iId comment id
     * @param array $aVals update data
     * @return mixed integer|boolean
     */
    public function updateComment($iId, $aVals)
    {
        return $this->addComment($aVals, $iId);
    }

    /**
     * Updates a comment
     *
     * @param int $iId comment id
     *
     * @internal param array $aVals update data
     * @return boolean
     */
    public function deleteComment($iId)
    {
        
    }
}

?>