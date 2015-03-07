<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage page : admincp\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Page_Controller_Admincp_Action extends Linko_Controller
{
	public function main()
	{
		$sAction = $this->getParam('action');

		$iId = (int)$this->getParam('id');

		$bEdit = false;

        $aVals = array();

        $aPage = array();
		
		switch($sAction)
		{
            case 'add':

                if($aVals = Input::post('val'))
                {
                    Linko::Validate()->set('add-page', array(
                        'page_title' => array(
                            'function' => 'required',
                            'error' => 'Title of the page is required.'
                        ),
                        'page_content' => array(
                            'function' => 'required',
                            'error' => 'You cannot create an empty page.'
                        )
                    ));

                    if(Linko::Validate()->isValid($aVals))
                    {
                        if(Linko::Model('Page/Action')->add($aVals))
                        {
                            Linko::Flash()->success('Page Created Successfully.');
                            Linko::Response()->redirect('page:admincp');
                        }
                    }
                }

                Linko::Template()->setBreadcrumb(array(
                    'Page' => Linko::Url()->make('page:admincp'),
                    'Add Page'
                ), 'Add Page')
                    ->setTitle('Add Page');

                break;
			case 'delete':
				if(Linko::Model('Page/Action')->delete($iId))
				{
					Linko::Flash()->success('Page Successfully Deleted');
					Linko::Response()->redirect('page:admincp');	
				}
			break;
			case 'edit':
				$aPage = Linko::Model('page')->getPage($iId, false, true);
				$bEdit = true;

				Linko::Validate()->set('add-page', array(
					'page_title' => array(
                        'function' => array(
                            'required'
                        ),
                        'error' => array(
                            'You must enter a valid title.'
                        )
                    )
				));

				if(($aVals = Input::post('val')))
				{
					if(Linko::Validate()->isValid($aVals))
					{
                        if(Linko::Model('Page/Action')->edit($iId, $aVals))
						{
							Linko::Flash()->success('Page Updated.');				
							Linko::Response()->redirect(Linko::Url()->make('page:admincp'));	
						}
					}
				}
                
				Linko::Template()->setBreadcrumb(array(
					'Page' => Linko::Url()->make('page:admincp'),
					'Edit'
				), 'Edit Page')
                ->setTitle('Edit Page');
			break;
		}

        if(Linko::Module()->isModule('editor'))
        {
            Linko::Model('editor')->set('js-editor-content', array());
        }

		Linko::Template()->setVars(array(
			'bEdit' => $bEdit,
            'aVals' => $aVals,
            'aPage' => $aPage,
            'aUserRoles' => Linko::Model('User/Role')->getRoles(),
            'aLayouts' => Linko::Model('Theme')->getLayouts()
		));
	}
}

?>