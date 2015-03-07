<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage blog : admincp\category\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Blog_Controller_Admincp_Category_Action extends Linko_Controller
{
    public function __construct($aParams)
    {
        parent::__construct($aParams);
        
        Linko::Template()->setBreadcrumb(array(
			'Blog' => Linko::Url()->make('blog:admincp'),
            'Categories' => Linko::Url()->make('blog:admincp:category')
		), 'Manage Categories')
		->setVars(array(
            
		))
        ->setTitle('Blog', 'Manage Categories');
    }
    
    public function main()
    {
        $aVals = array();
        
        $aCategory = array();
        
        $sAction = $this->getParam('action');
        
        $iCategory = $this->getParam('id');
        
        $aValidate = array(
            'title' => array('function' => 'required', 'error' => 'The title cannot be empty.')
        );
        
        switch($sAction)
        {
            case 'add':
                if($aVals = Input::post('val'))
                {
                    Linko::Validate()->set('add-category', $aValidate);
                    
                    if(Linko::Validate()->isValid($aVals))
                    {
                        if(Linko::Model('Blog/Action')->addCategory($aVals))
                        {
                            Linko::Flash()->success('Category added.');
                            Linko::Response()->redirect('blog:admincp:category');
                        }
                    }
                }
                
                Linko::Template()->setBreadcrumb(array('Add' => null), 'Add Category');
                
                break;
            case 'edit':
                $aCategory = Linko::Model('Blog')->getCategory($iCategory);
                
                if($aVals = Input::post('val'))
                {
                    Linko::Validate()->set('add-category', $aValidate);
                    
                    if(Linko::Validate()->isValid($aVals))
                    {
                        if(Linko::Model('Blog/Action')->updateCategory($iCategory, $aVals))
                        {
                            Linko::Flash()->success('Category updated.');
                            Linko::Response()->redirect('blog:admincp:category');
                        }
                    }
                }
                
                Linko::Template()->setBreadcrumb(array('Edit' => null), 'Edit Category');
                
                break;
            case 'delete':
                if(Linko::Model('Blog/Action')->deleteCategory($iCategory))
                {
                    Linko::Flash()->success('Category deleted.');
                    Linko::Response()->redirect('blog:admincp:category');
                }            
                break;
        }
        
        Linko::Template()->setVars(array(
            'iCategory' => $iCategory,
            'aVals' => $aVals,
            'aCategory' => $aCategory
        ));
    }
}

?>