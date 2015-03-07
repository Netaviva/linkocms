<?php

defined('LINKO') or exit();

class Block_Plugin_Page
{
	public function controller_view_end($iPageId)
	{
		$aBlocks = Linko::Model('Block')->getPageBlocks($iPageId);

		// Set Blocks
		foreach(array_keys($aBlocks) as $sPosition)
		{
			foreach($aBlocks[$sPosition] as $aBlock)
			{
				// if this block is dissallowed for this user role, lets skip it
				if(in_array(Linko::Model('User/Auth')->getUserBy('role_id'), $aBlock['dissallow_access']))
				{
					continue;
				}

				Linko::Module()->setBlocks($sPosition, array( // sidebar
					$aBlock['component_file'], //
					$aBlock['block_param'],
					$aBlock['block_title']
				));
			}
		}
	}
}

?>