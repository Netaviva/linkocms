<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage blog : admincp\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Blog_Controller_Admincp_Action extends Linko_Controller
{
    public function main()
    {
        $sAction = $this->getParam('action');
        $aVals = array();
        $aPost = array();
        $aPostCategories = array();
        $bEdit = false;
        $iId = $this->getParam('id');
        
        $aCategories = Linko::Model('Blog')->getCategories(false);
        
        Linko::Template()->setBreadcrumb(array(
			'Blog' => Linko::Url()->make('blog:admincp'),
		), 'Blog')
        ->setStyle('admincp.css', 'module_blog_css');

        Linko::Validate()->set('add_post', array(
            'title' => array('function' => 'required', 'error' => 'Title cannot be empty.'),
            'content' => array('function' => 'required', 'error' => 'You cannot create an empty post.'),
        ));
        
        if(Linko::Module()->isModule('editor'))
        {
            Linko::Model('editor')->set('post-content', array());
        } 
                                
        switch($sAction)
        {
            case 'add':
                if($aVals = Input::post('val'))
                {                    
                    if(Linko::Validate()->isValid($aVals))
                    {
                        if(Linko::Model('Blog/Action')->addPost($aVals))
                        {
                            Linko::Flash()->success('Blog post added successfully.');
                            Linko::Response()->redirect('blog:admincp');
                        }
                    }
                }
                
                Linko::Template()->setBreadcrumb(array('Create'), 'Add New Post')
					->setTitle('Add New Post');
                                   
                break;
            case 'edit':
                $aPost = Linko::Model('Blog')->getPostById($iId, false, true);
                
                $aPostCategories = Linko::Model('Blog')->getPostCategories($iId, true);
                
                $bEdit = true;

                if($aVals = Input::post('val'))
                {                    
                    if(Linko::Validate()->isValid($aVals))
                    {
                        if(Linko::Model('Blog/Action')->updatePost($aVals, $iId))
                        {
                            Linko::Flash()->success('Post updated successfully.');
                            Linko::Response()->redirect('blog:admincp');
                        }
                    }
                }
                
                Linko::Template()->setBreadcrumb(array('Edit'), 'Edit Post')
					->setTitle('Edit Post');                
                break;
            case 'delete':
                
                if(Linko::Model('Blog/Action')->deletePost($iId))
                {
                    Linko::Flash()->success('Post Deleted.');
                    Linko::Response()->redirect('blog:admincp');                    
                }
                
                break;
        }
        
        Linko::Template()->setVars(array(
            'aVals' => $aVals,
            'aPost' => $aPost,
            'bEdit' => $bEdit,
            'aCategories' => $aCategories,
            'aPostCategories' => $aPostCategories,
        ));	       
    }
}

?>