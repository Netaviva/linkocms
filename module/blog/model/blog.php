<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage blog : model - blog.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Blog_Model_Blog extends Linko_Model
{
    /**
     * Gets a single blog post
     *
     * @param mixed   $mId    post id or post url slug
     * @param boolean $bParse uses shortcode to parse content if set to true
     * @param bool    $bOverrideApprove
     *
     * @return array
     */
    public function getPost($mId, $bParse = true, $bOverrideApprove = false)
    {
        Linko::Cache()->set(array('blog', 'post_' . $mId . '_' . ($bParse ? 'parsed' : 'original')));
        
        if(!$aPost = Linko::Cache()->read())
        {
            $sCond = (is_numeric($mId) ? "p.post_id = :mid" : "p.post_slug = :mid");
            
      		$oQuery = Linko::Database()->table('blog_post', 'p')
                ->select("p.*, " . Linko::Model('User')->getTableFields('u'))
    			->leftJoin('user', 'u', 'u.user_id = p.author_id')
                ->where($sCond);
            
            if(!$bOverrideApprove)
            {
                $oQuery->where("p.is_approved", '=', true);
            }  
            
            $aPost = $oQuery->query(array(':mid' => $mId))->fetchRow();

	        if(!isset($aPost['post_id']))
	        {
				return array();
	        }

            $this->preparePost($aPost, $bParse);

            Linko::Cache()->write($aPost);
        }
        
        return $aPost;
    }
 
	/**
	 * See getPost()
	 * 
	 */	   
	public function getPostBySlug($sSlug, $bParse = true, $bOverrideApprove = false)
	{		
        return $this->getPost((string)$sSlug, $bParse, $bOverrideApprove);	
	}

	/**
	 * See getPost()
	 * 
	 */	
	public function getPostById($iId, $bParse = true, $bOverrideApprove = false)
	{		
        return $this->getPost((int)$iId, $bParse, $bOverrideApprove);	
	}
    	
	public function getRecentPosts()
	{
        $iLimit = Linko::Module()->getSetting('blog.recent_post_limit');
        
		return Linko::Database()->table('blog_post')
            ->select()
            ->order('time_created DESC')
            ->where('is_approved', '=', true)
            ->limit($iLimit)
            ->query()->fetchRows();
	}

    /**
     * Get blog categories
     *
     * @param bool $bPostCount
     * @param int  $iLimit number of categories to return
     *
     * @return array
     */
	public function getCategories($bPostCount = true, $iLimit = null)
	{
        $oQueryCategory = Linko::Database()->table('blog_category', 'bc')
            ->select('bc.*');

        if($bPostCount)
        {
            //$oQueryCategory->select('COUNT("p.post_id") AS total_post')
                //->leftJoin('blog_category_post', 'bcp', 'bcp.category_id = bc.category_id')
                //->leftJoin('blog_post', 'p', 'p.post_id = bcp.post_id AND p.is_approved = 1');

	        $oQueryCategory->select('COUNT("bcp.post_id") AS total_post')
		        ->leftJoin('blog_category_post', 'bcp', 'bcp.category_id = bc.category_id AND bcp.post_id IN (SELECT `post_id` FROM ' . Linko::Database()->prefix('blog_post') . ' WHERE is_approved = 1)');
        }

        if($iLimit)
        {
            $oQueryCategory->limit($iLimit);
        }

        $aRows = $oQueryCategory->group('bc.category_id')->query()->fetchRows();

		return $aRows;
	}
    
    public function getCategory($iId)
    {
        return Linko::Database()->table('blog_category', 'bc')
            ->select('bc.*', 'COUNT("post_id") AS total_post')
            ->leftJoin('blog_category_post', 'bcp', 'bcp.category_id = bc.category_id')
            ->where('bc.category_id', '=', $iId)
            ->query()
            ->fetchRow();
    }
    
    public function getPostCategories($iId, $bReturnId = false)
    {
        $aRows = Linko::Database()->table('blog_category_post', 'bcp')
            ->select('bc.*')
            ->leftJoin('blog_category', 'bc', 'bc.category_id = bcp.category_id')
            ->where('bcp.post_id', '=', $iId)
            ->query()
            ->fetchRows();
            
        if($bReturnId)
        {
            $aCategories = array();
            
            foreach($aRows as $aRow)
            {
                $aCategories[] = $aRow['category_id'];
            }
            
            return $aCategories;
        }
        
        return $aRows;
    }

    /**
     * Checks to see if comment is possible.
     *
     * @return boolean
     */
    public function isCommentEnabled()
    {
        return (bool)(Linko::Module()->getSetting('blog.enable_default_comment') && Linko::Module()->isModule('comment'));
    }

    public function preparePost(&$aRow, $bParse)
    {
        $sDateFormat = Linko::Config()->get('date.format');

        $aRow['post_text_orig'] = $aRow['post_text'];
        $aRow['post_text'] = $bParse ? nl2br(Linko::Shortcode()->parse($aRow['post_text'])) : $aRow['post_text'];
        $aRow['time_created_unix'] = $aRow['time_created'];
        $aRow['time_created'] = Date::getTime($sDateFormat, $aRow['time_created']);
        $aRow['time_created_readable'] = Date::timeAgo($aRow['time_created_unix'], $sDateFormat);
        $aRow['time_updated_unix'] = $aRow['time_updated'];
        $aRow['time_updated'] = Date::getTime($sDateFormat , $aRow['time_updated']);
        $aRow['post_url'] = Linko::Url()->make('blog:entry', array('slug' => $aRow['post_slug']));
        $aRow['user_url'] = Linko::Url()->make('user:profile', array('username' => $aRow['username']));
        $aRow['category'] = Linko::Model('Blog')->getPostCategories((int)$aRow['post_id']);
        $aRow['total_comments'] = $this->isCommentEnabled() ? Linko::Model('Comment')->getTotalComments($aRow['post_id'], 'blog') : 0;

        return $aRow;
    }
}

?>