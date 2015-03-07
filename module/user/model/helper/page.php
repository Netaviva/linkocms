<?php

class User_Model_Helper_Page extends Linko_Model
{
	public function getPagesForSettings()
	{
		$aPages = Linko::Model('Page')->getPages();
		$aSelect = array();

		foreach($aPages as $aPage)
		{
			$aSelect[$aPage['page_id']] = $aPage['page_title'];
		}

		return $aSelect;
	}
}