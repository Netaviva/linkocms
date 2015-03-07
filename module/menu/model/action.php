<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage menu : model - action.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Menu_Model_Action extends Linko_Model
{
    public function addMenu($aVals, $iId = null)
    {
        $bUpdate = $iId ? true : false;

        if(!isset($aVals['title']))
        {
            return false;
        }

        $aData  = array('menu_title' => $aVals['title']);

        if(isset($aVals['location']) && $aVals['location'] != '')
        {
            $aData['menu_location'] = $aVals['location'];
        }

        if($bUpdate)
        {
            Linko::Database()->table('menu')
                ->update($aData)
                ->where('menu_id', '=', $iId)
                ->query();

            return $iId;
        }
        else
        {

            // Check if this menu title exists. Then just return the id
            if($iExistsId = Linko::Database()->table('menu')
                ->select('menu_id')
                ->where('menu_title', '=', $aVals['title'])
                ->query()->fetchValue()) {

                return $iExistsId;
            }

            $iId = Linko::Database()->table('menu')
                ->insert($aData)
                ->query()
                ->getInsertId();

            return $iId;
        }

        return false;
    }

    public function updateMenu($iId, $aVals)
    {
        return $this->addMenu($aVals, $iId);
    }

    public function deleteMenu($iId)
    {
        Linko::Database()->table('menu')
            ->delete()
            ->where('menu_id', '=', $iId)
            ->query();

        return true;
    }

	public function addMenuItem($aVals, $iId = null)
	{
		$bEdit = $iId ? true : false;
        $aData = array('menu_item_url' => isset($aVals['url']) ? $aVals['url'] : null);

        if(!$bEdit)
        {
            if(!isset($aVals['menu_id']) || !isset($aVals['title']))
            {
                return false;
            }

            $aData['menu_id'] = $aVals['menu_id'];
        }

		if($bEdit == false)
		{
			$aVals = array_merge(array(
				'status' => 0,
				'target' => 0,
				'param' => array(),
				'allow_access' => array(),
				'page_id' => 0,
                'parent_id' => 0
			), $aVals);
		}

        if(array_key_exists('title', $aVals))
        {
            $aData['menu_item_title'] = $aVals['title'];
        }

		if(array_key_exists('status', $aVals))
		{
			$aData['menu_item_status'] = $aVals['status'];
		}

		if(array_key_exists('target', $aVals))
		{
			$aData['menu_item_target'] = $aVals['target'];
		}

		if(array_key_exists('page_id', $aVals))
		{
			$aData['page_id'] = $aVals['page_id'];
		}

		if(array_key_exists('param', $aVals))
		{
			if(is_string($aVals['param']))
			{
				parse_str($aVals['param'], $aVals['param']);
			}

			$aData['menu_item_param'] = serialize($aVals['param']);
		}

		if(array_key_exists('allow_access', $aVals))
		{
			$aData['allow_access'] = serialize($aVals['allow_access']);
		}

        if(array_key_exists('parent_id', $aVals))
        {
            $aData['parent_id'] = $aVals['parent_id'];
        }

        //exit(Arr::dump($aData));

		if($bEdit)
		{
            // Edit
			Linko::Database()->table('menu_item')
                ->update($aData)
                ->where('menu_item_id', '=', $iId)
                ->query();

            // Call the locale api to update translation
            Linko::Model('Locale/Language/Action')->updateTranslation('menu_translation_' . $iId, $aVals['title'], 'menu');
                			
            Linko::Cache()->delete(array('menu', 'main_menu')); // @todo main_menu
            
			return true;
		}
		else
		{
            // Create
			$iOrder = ((int)(Linko::Database()->table('menu_item')
				->select('MAX(menu_item_order) as max_order')
				->where('menu_id', '=', $aVals['menu_id'])
				->query()
				->fetchValue()) + 1);

            $aData['menu_item_order'] = $iOrder;
			
            if($iId = Linko::Database()->table('menu_item')->insert($aData)->query()->getInsertId())
            {
                // Call the locale api to add translation
                Linko::Model('Locale/Language/Action')->addTranslation('menu_translation_' . $iId, $aVals['title'], 'menu');
                
                // Refresh Cache
                Linko::Cache()->delete('menu', 'dir');
                
                return true;   
            }	
		}
		
		return false;
	}

    public function updateMenuItem($iId, $aVals)
    {
        return $this->addMenuItem($aVals, $iId);
    }

    public function deleteMenuItem($iId)
    {
        Linko::Database()->table('menu_item')
            ->delete()
            ->where('menu_item_id', '=', $iId)
            ->query();

        return true;
    }
    
    public function updateOrder($aOrder)
    {
        foreach($aOrder as $iId => $iOrder)
        {
            $iOrder = (int)$iOrder;
            
            Linko::Database()->table('menu_item')
                ->update(array('menu_item_order' => $iOrder))
                ->where('menu_item_id', '=', $iId)
                ->query();
        }

        return true;
    }

	public function createCollection($aVals)
	{
		if(!isset($aVals['title']))
		{
			return Linko::Error()->set(Lang::t('menu.title_required'));
		}

		return 1;
	}

	public function deleteCollection($iId)
	{

	}
}

?>