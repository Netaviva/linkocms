<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage menu : model - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Menu_Model_Admincp extends Linko_Model
{
	private $_aLocation = array(
		'main_menu' => 'Main Menu',
		'footer' => 'Footer',
	);
	
	public function getMenu($iId)
	{
		$aRow = Linko::Database()->table('menu')
			->select()
			->where("menu_id = :id")
			->query(array(':id' => $iId))
			->fetchRow();
		
		$aRow['menu_item_param'] = $aRow['menu_item_param'] == '' ? '' : http_build_query(unserialize($aRow['menu_item_param']));
		
		$aRow['allow_access'] = $aRow['allow_access'] == '' ? array() : unserialize($aRow['allow_access']);
		
		return $aRow;		
	}
	
	public function getLocation($sLocation = null)
	{
		return ($sLocation) ? (isset($this->_aLocation[$sLocation]) ? $this->_aLocation[$sLocation] : $this->_aLocation) : $this->_aLocation;	
	}
}

?>