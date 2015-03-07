<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage blog : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Blog_Plugin_Admincp
{
    public function init()
    {
        // Posts
        Linko::Model('Admincp')->addRoute('blog(/[page])', array(
            'id' => 'blog:admincp',
            'controller' => 'blog/admincp/index',
            'rules' => array(
                'page' => ':int'
            )
        ));
               
        Linko::Model('Admincp')->addRoute('blog/[action](/[id])', array(
            'id' => 'blog:admincp:action',
            'controller' => 'blog/admincp/action',
            'rules' => array(
                'action' => 'add|edit|delete',
                'id' => ':int'
            )
        ));
        
        // Category
        Linko::Model('Admincp')->addRoute('blog/category', array(
            'id' => 'blog:admincp:category',
            'controller' => 'blog/admincp/category/index'
        ));
        
        Linko::Model('Admincp')->addRoute('blog/category/[action](/[id])', array(
            'id' => 'blog:admincp:category:action',
            'controller' => 'blog/admincp/category/action',
            'rules' => array(
                'action' => 'add|edit|delete',
                'id' => ':int'
            )
        ));
                        
        Linko::Model('Admincp')->addMenu('Blog', array(
            'Add Post' => Linko::Url()->make('blog:admincp:action', array('action' => 'add')), // blog/add
            'Posts' => Linko::Url()->make('blog:admincp'), // blog
            'Category' => Linko::Url()->make('blog:admincp:category'), // blog/category
        ));     
    }
    
    // called in admincp/controller/dashboard
    public function con_dashboard()
    {

    }
}
?>