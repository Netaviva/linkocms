<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage blog : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Blog_Model_Action extends Linko_Model
{
    public function addPost($aVals, $iId = null)
    {
        $bEdit = false;
        
        if($iId)
        {
            $bEdit = true;
        }
        
        $aVals['slug'] = $aVals['slug'] != '' ? $aVals['slug'] : Inflector::slugify($aVals['title']);
        
        $aData = array(
            'post_title' => $aVals['title'],
            'post_slug' => $aVals['slug'],
            'post_text' => $aVals['content'],
            'is_approved' => isset($aVals['approve']) ? true : false,
        );
        
        $oPostExists = Linko::Database()->table('blog_post')
            ->select()
            ->where('post_slug', '=', $aVals['slug']);
        
        if($bEdit)
        {
            $bExists = $oPostExists->where('post_id', '!=', $iId)->query()->getCount();
            
            // Check if the blog title/slug already exists
            if($bExists)  
            {
                return Linko::Error()->set('A post like this already exists.');
            }
            
            $aData['time_updated'] = time();

            Linko::Database()->table('blog_post')
                ->update($aData)
                ->where('post_id', '=', $iId)
                ->query();
            
            if(isset($aVals['category']))
            {
                $this->setPostCategories($iId, $aVals['category']);
            }
            
            Linko::Plugin()->call('blog.update_post', $iId, $aVals);
        }
        else
        {
            $bExists = $oPostExists->query()->getCount();
            
            // Check if the blog title/slug already exists
            if($bExists)  
            {
                return Linko::Error()->set('A post like this already exists.');
            }
            
            $aData['time_created'] = time();
            $aData['time_updated'] = time();
            $aData['author_id'] = Linko::Model('User/Auth')->getUserId();
            
            $iPostId = Linko::Database()->table('blog_post')
                ->insert($aData)
                ->query()
                ->getInsertId();

            if(isset($aVals['category']))
            {
                $this->setPostCategories($iPostId, $aVals['category']);
            }
                        
            Linko::Plugin()->call('blog.add_post', $iPostId, $aVals);            
        }
        
        Linko::Cache()->delete('blog', 'dir');
        
        return true;
    }
    
    public function updatePost($aVals, $iId)
    {
        return $this->addPost($aVals, $iId);
    }
    
    public function deletePost($iId)
    {
        Linko::Database()->table('blog_post')
			->delete()
			->where("post_id = :id")
			->query(array(':id' => $iId));
        
        Linko::Plugin()->call('blog.delete_post', $iId);
        
        Linko::Cache()->delete('blog', 'dir');
        
        return true;
    }
    
    public function approvePost($mId)
    {
        if(!is_array($mId))
        {
            $mId = array($mid);
        }
        
        if(!count($mId))
        {
            return true;
        }

        Linko::Database()->table('blog_post')
			->update(array(
                'is_approved' => true
            ))
			->whereIn('post_id', $mId)
			->query();
        
        Linko::Plugin()->call('blog.approve_post', $mId);
        
        Linko::Cache()->delete('blog', 'dir');
        
        return true;
    }
    
    public function unapprovePost($mId)
    {
        if(!is_array($mId))
        {
            $mId = array($mid);
        }

        if(!count($mId))
        {
            return false;
        }
        
        Linko::Database()->table('blog_post')
			->update(array(
                'is_approved' => false
            ))
			->whereIn('post_id', $mId)
			->query();
        
        Linko::Plugin()->call('blog.unapprove_post', $mId);
        
        Linko::Cache()->delete('blog', 'dir');
        
        return true;        
    }
    
    public function addCategory($aVals, $iId = null)
    {
        $bEdit = false;
        
        if($iId)
        {
            $bEdit = true;
        }

        if(empty($aVals['slug']))
        {
            $aVals['slug'] = Inflector::slugify($aVals['title']);
        }
        
        $aData = array(
            'category_title' => $aVals['title'],
            'category_slug' => $aVals['slug']
        );
               
        $oExists = Linko::Database()->table('blog_category')
            ->select('category_id')
            ->where(function($oQuery) use ($aVals)
            {
                $oQuery->where('category_title', '=', $aVals['title']);
                $oQuery->orWhere('category_slug', '=', $aVals['slug']);
            });
        
        if($bEdit)
        {
            if($oExists->where('category_id', '!=', $iId)->query()->getCount())
            {
                Linko::Error()->set('A category with this title/slug already exists.');
                
                return false;
            } 
            
            Linko::Database()->table('blog_category')
                ->update($aData)
                ->where('category_id', '=', $iId)
                ->query(); 
            
            Linko::Plugin()->call('blog.update_category', $iId, $aData);
            
            return $iId;          
        }
        else
        {
            if($oExists->query()->getCount())
            {
                Linko::Error()->set('A category with this title/slug already exists.');
                
                return false;
            }
            
            $iInsertId = Linko::Database()->table('blog_category')
                ->insert($aData)
                ->query()
                ->getInsertId();
            
            Linko::Plugin()->call('blog.add_category', $iId, $aData);
            
            return $iInsertId;        
        }
    }
    
    public function updateCategory($iId, $aVals)
    {
        return $this->addCategory($aVals, $iId);
    }
    
    public function deleteCategory($iId)
    {
        Linko::Database()->table('blog_category')
			->delete()
			->where('category_id', '=', $iId)
			->query();

        Linko::Database()->table('blog_category_post')
			->delete()
			->where('category_id', '=', $iId)
			->query();
                    
        Linko::Plugin()->call('blog.delete_category', $iId);
        
        return true;
    }
    
    public function addPostCategory($iPostId, $mCategories)
    {
        if(!is_array($mCategories))
        {
            $mCategories = array($mCategories);
        }
        
        foreach($mCategories as $iCategory)
        {
            Linko::Database()->table('blog_category_post')
                ->insert(array(
                    'post_id' => (int)$iPostId,
                    'category_id' => (int)$iCategory, 
                ))
                ->query();
        }
        
        Linko::Cache()->delete('blog', 'dir');

        Linko::Plugin()->call('blog.add_post_category', $iPostId, $mCategories);

        return true;
    }
   
    public function setPostCategories($iPostId, $mCategories)
    {
        // first delete all categories that this post has been assigned
        Linko::Database()->table('blog_category_post')
            ->delete()
            ->where('post_id', '=', $iPostId)
            ->query();
            
        $this->addPostCategory($iPostId, $mCategories);
        
        Linko::Cache()->delete('blog', 'dir');
            
        return true;
    }
}

?>