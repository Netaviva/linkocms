<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage blog : model - browse.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Blog_Model_Browse extends Linko_Model
{
    private $_bApproved = true;
    
    private $_sArchive;
    
    private $_mId;
    
    private $_iLimit;
    
    private $_iPage;
    
    public function __construct()
    {
        
    }
    
    public function archive($sArchive = null, $mId = null)
    {
        $this->_sArchive = $sArchive;
        
        $this->_mId = $mId;
        
        return $this;
    }

    public function approved($bApproved)
    {
        $this->_bApproved = $bApproved;
        
        return $this;
    }

    public function limit($iLimit)
    {
        $this->_iLimit = $iLimit;
        
        return $this;
    }

    public function page($iPage)
    {
        $this->_iPage = $iPage;
        
        return $this;
    }
              
    public function get()
    {
        $sCache = Linko::Cache()->set(array('blog', 'browse_' . md5($this->_sArchive . $this->_mId . $this->_iLimit . $this->_iPage . $this->_bApproved)));
        
        if(!$aData = Linko::Cache()->read($sCache))
        {
            $oBrowse = Linko::Database()->table('blog_post', 'p')           
                ->select('p.*, u.username, u.email')
                ->leftJoin('user', 'u', 'u.user_id = p.author_id');
            
            if($this->_sArchive)
            {
                if($this->_sArchive == 'category')
                {
                    if(!is_int($this->_mId))
                    {
                        $this->_mId = Linko::Database()->table('blog_category')
                            ->select('category_id')
                            ->where('category_slug', '=', $this->_mId)
                            ->query()
                            ->fetchValue();
                    }
                    
                    $oBrowse->leftJoin('blog_category_post', 'bcp', 'bcp.post_id = p.post_id')
                        ->where('bcp.category_id', '=', $this->_mId);              
                }
            }
            
            if($this->_bApproved)
            {
                $oBrowse->where('p.is_approved', '=', true);
            }

            Linko::Plugin()->filter('blog.model_browse_posts_filter', $oBrowse);
            
            list($iTotal, $aRows) = $oBrowse->order('p.time_created', 'DESC')
                ->group('p.post_id')
                ->query()
                ->paginate($this->_iPage, $this->_iLimit);
            
            foreach($aRows as $iKey => $aRow)
            {
                Linko::Model('Blog')->preparePost($aRow, false);

                $aRows[$iKey] = $aRow;
                $aRows[$iKey]['post_text'] = Str::truncate(strip_tags($aRow['post_text']), 100, '...', Str::TRUNCATE_WORD);
            }
            
            $aData = array($iTotal, $aRows);
            
            Linko::Cache()->write($aData, $sCache); 
        }
        
        return $aData;
    }
}

?>