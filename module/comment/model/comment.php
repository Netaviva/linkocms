<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage comment : model - comment.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Comment_Model_Comment extends Linko_Model
{
    public function init()
    {
        Linko::Template()->setScript('jquery/jquery.scrollTo.js', 'asset_js')
            ->setScript('comment.js', 'module_comment')
            ->setStyle('comment.css', 'module_comment')
            ->setTranslation(array('comment.posting_your_comment'));
    }

    public function getComments($iItemId, $sModule, $iLimit = 0)
    {
        $aRows = Linko::Database()->table('comment', 'c')
            ->select('c.*, ' . Linko::Model('User')->getTableFields('u'))
            ->leftJoin('user', 'u', 'u.user_id = c.user_id')
            ->where('item_id', '=', $iItemId)
            ->where('module_id', '=', $sModule)
            ->query()
            ->fetchRows();
            
        $aComments = array();

        foreach($aRows as $iKey => $aRow)
        {
            $aRow['time_created_unix'] = $aRow['time_created'];
            $aRow['time_created'] = Date::timeAgo($aRow['time_created'], Linko::Config()->get('date.format'));

            if($aRow['parent_comment_id'] == 0)
            {
                $aComments[$aRow['comment_id']] = $aRow;
                $aComments[$aRow['comment_id']]['replies'] = array();
            }
            else
            {
                $aComments[$aRow['parent_comment_id']]['replies'][] = $aRow;
            }
        }

        if($iLimit)
        {
            return array(
                array_slice($aComments, 0, $iLimit),
                count($aComments)
            );
        }
        
        return array($aComments, count($aComments));
    }

    public function getTotalComments($iItemId, $sModule)
    {
        return Linko::Database()->table('comment')
            ->select()
            ->where('item_id', '=', $iItemId)
            ->where('module_id', '=', $sModule)
            ->query()
            ->getCount();
    }

    public function getComment($iId)
    {
        $aRow = Linko::Database()->table('comment', 'c')
            ->select('c.*, ' . Linko::Model('User')->getTableFields('u'))
            ->leftJoin('user', 'u', 'u.user_id = c.user_id')
            ->where('comment_id', '=', $iId)
            ->query()
            ->fetchRow();

        $aRow['time_created_unix'] = $aRow['time_created'];
        $aRow['time_created'] = Date::timeAgo($aRow['time_created_unix'], Linko::Config()->get('date.format'), $aRow['time_created']);

        return $aRow;
    }
}

?>