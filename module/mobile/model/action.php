<?php

/**
 * Action Class
 *
 * @package Mobile Module
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class Mobile_Model_Action extends Linko_Model
{
    /**
     * Update and Insert Login in the same method
     * so as to avoid repeating codes. (DRY)
     *
     * @param array $aVals insert data
     * @param int $iId used for update
     *
     * @return array
     */
    public function addDashboardItem($aVals, $iId = null)
    {
        $bUpdate = false;

        if($iId)
        {
            // If an id is passed as second argument,
            // it means we want to update. Set the update flag to true.
            $bUpdate = true;
        }

        if(!$bUpdate)
        {
            // if we are not updating, we have to make sure
            // these two keys are provided
            if(!Arr::hasKeys($aVals, 'title', 'page_id'))
            {
                return array(false, $aVals);
            }
        }

        $aData = array();

        // keys are checked and added to $aData array in case during updates
        // it may not be required to provide all details
        if(array_key_exists('title', $aVals))
        {
            $aData['item_title'] = $aVals['title'];
        }

        if(array_key_exists('page_id', $aVals))
        {
            $aData['page_id'] = $aVals['page_id'];
        }

        if(array_key_exists('order', $aVals))
        {
            $aData['item_order'] = $aVals['order'];
        }

        if($bUpdate)
        {
            // Update Process
            Linko::Database()->table('mobile_dashboard')
                ->update($aData)
                ->where('item_id', '=', $iId)
                ->query();

            Linko::Plugin()->call('mobile.update_dashboard_item', $iId, $aData);

            return array(true, array_merge(array('item_id' => $iId), $aData));
        }
        else
        {
            // Add Process

            // Get the Last Order Number
            $iOrder = (1 + (int)Linko::Database()->table('mobile_dashboard')
                ->select('MAX("item_order") as max_item_order')
                ->query()->fetchValue());

            $aData['item_order'] = $iOrder;

            $iId = Linko::Database()->table('mobile_dashboard')
                ->insert($aData)
                ->query()
                ->getInsertId();

            Linko::Plugin()->call('mobile.add_dashboard_item', $iId, $aData);

            return array(true, array_merge(array('item_id' => $iId), $aData));
        }
    }

    /**
     * @param int $iId dashboard item id
     * @param array $aVals update data
     * @see addDashboardItem()
     *
     * @return array
     */
    public function updateDashboardItem($iId, $aVals)
    {
        return $this->addDashboardItem($aVals, $iId);
    }

    /**
     * @param int $iId Dashboard Item ID
     *
     * @return bool
     */
    public function deleteDashboardItem($iId)
    {
        return (bool)(Linko::Database()->table('mobile_dashboard')
            ->delete()
            ->where('item_id', '=', $iId)
            ->query()
            ->getAffectedRows());
    }

    /**
     * Updates the order of the dashboard items
     *
     * @param array $aOrder Order
     *
     * @return bool
     */
    public function updateDashboardOrder($aOrder)
    {
        foreach($aOrder as $iId => $iOrder)
        {
            $iOrder = (int)$iOrder;

            Linko::Database()->table('mobile_dashboard')
                ->update(array('item_order' => $iOrder))
                ->where('item_id', '=', $iId)
                ->query();
        }

        return true;
    }
}