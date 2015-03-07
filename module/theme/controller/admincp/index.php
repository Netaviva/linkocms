<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage theme : admincp\index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Theme_Controller_Admincp_Index extends Linko_Controller
{
	public function main()
	{
		$aThemes = Linko::Model('Theme')->getThemes();
        
		if($aVals = Input::post('default'))
		{
			if(Linko::Model('Theme')->setDefault($aVals['theme'], $aVals['type']))
			{
				Linko::Flash()->success($aVals['theme'] . ' theme set as default for type ' . $aVals['type']);
				Linko::Response()->redirect('self');	
			}
		}
		
		Linko::Template()->setBreadcrumb(array(
			'Theme',
		), 'Manage Themes')
        ->setTitle('Manage Themes')
		->setVars(array(
				'aThemes' => $aThemes,
                'aDefaultThemes' => Linko::Model('Theme')->getDefaultThemes()	
			)
		);
	}
}

?>