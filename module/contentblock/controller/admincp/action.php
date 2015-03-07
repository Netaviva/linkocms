<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage contentblock : admincp\action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Contentblock_Controller_Admincp_Action extends Linko_Controller
{
	public function main()
	{
		// Hello
		$sAction = $this->getParam('action');
		$iId = $this->getParam('id');
		$aVals = array();
		$aBlock = array();
		
		Linko::Template()->setBreadcrumb(array('Content Blocks' => Linko::Url()->make('contentblock:admincp')))
			->setTitle('Content Block');

//        if(Linko::Module()->isModule('editor'))
//        {
//            Linko::Model('Editor')->set('contentblock-text', array());
//        }
        		
		switch($sAction)
		{
			case 'add':
				if($aVals = Input::post('val'))
				{
					$oValidate = Linko::Validate()->set('create_block', array(
						'title' => array('function' => 'required', 'error' => 'Must specify a block title.'),
						'text' => array('function' => 'required', 'error' => 'A content block must contain "a content".'),
					));
					
					if($oValidate->isValid($aVals))
					{					
						if(Linko::Model('Contentblock/Action')->add($aVals))
						{
							Linko::Flash()->success('Content Block Successfully Created.');
							Linko::Response()->redirect('contentblock:admincp');
						}
					}
				}
				
				Linko::Template()->setBreadcrumb(array('Create'), 'Create Content Block')
					->setTitle('Create');
				
				break;
			case 'edit':
				
				$aBlock = Linko::Model('Contentblock')->get($iId);
				
				if($iId && ($aVals = Input::post('val')))
				{
					$oValidate = Linko::Validate()->set('edit_block', array(
						'title' => array('function' => 'required', 'error' => 'Must specify a block title.'),
						'text' => array('function' => 'required', 'error' => 'A content block must contain "a content".'),
					));
					
					if($oValidate->isValid($aVals))
					{
						if(Linko::Model('Contentblock/Action')->add($aVals, $iId, $aBlock['component_id']))
						{
							Linko::Flash()->success('Content Block Successfully Updated.');
							Linko::Response()->redirect('contentblock:admincp');
						}
					}						
				}

				Linko::Template()->setBreadcrumb(array('Edit'), 'Edit Content Block')
					->setTitle('Edit');
					
				break;
            case 'delete':
                if(Linko::Model('Contentblock/Action')->delete($iId))
                {
                    Linko::Flash()->success('Content block Successfully Deleted');
                    Linko::Response()->redirect('contentblock:admincp');
                }
                break;
		}
		
		Linko::Template()->setVars(array(
				'aVals' => $aVals,
				'aBlock' => $aBlock
			)
		);
	}
}

?>