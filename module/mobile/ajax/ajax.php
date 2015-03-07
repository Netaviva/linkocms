<?php

class Mobile_Ajax extends Linko_Ajax
{
    public function addDashboardItem($iId = null)
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->toJson(array('error' => true, 'data' => array()));
        }

        $bUpdate = false;

        if($iId)
        {
            $bUpdate = true;
        }

        Linko::Validate()->set('ajax_add_dashboard_item', array(
            'title' => array(
                'function' => 'required',
                'error' => 'Title is required'
            ),
            'page_id' => array(
                'function' => 'required',
                'error' => 'Page that this item links to is required'
            ),
        ));

        if(Linko::Validate()->isValid($this->getParam()))
        {
            if($bUpdate)
            {
                list($bStatus, $aItem) = Linko::Model('Mobile/Action')->updateDashboardItem(
                    $iId,
                    $this->getParam()
                );
            }
            else
            {
                list($bStatus, $aItem) = Linko::Model('Mobile/Action')->addDashboardItem($this->getParam());
            }

            if($bStatus)
            {
                $aItem = Linko::Model('Mobile')->getDashboardItem($aItem['item_id']);

                return $this->toJson(array('error' => false, 'data' => $aItem));
            }
        }
        else
        {
            // gets all errors generated from validation
            $aErrors = Linko::Error()->get();

            // Just want to return the first error
            $sFirstError = $aErrors[0];

            return $this->toJson(array('error' => true, 'data' => $sFirstError));
        }

        return $this->toJson(array('error' => true, 'data' => $aItem));
    }

    public function updateDashboardItem()
    {
        $iId = (int)$this->getParam('item_id');

        return $this->addDashboardItem($iId);
    }

    public function getDashboardItem()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->toJson(array('error' => true, 'data' => array()));
        }

        return $this->toJson(array(
            'error' => false,
            'data' => Linko::Model('Mobile')->getDashboardItem((int)$this->getParam('item_id'))
        ));
    }

    public function deleteDashboardItem()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->toJson(array('error' => true, 'deleted' => false));
        }

        if(Linko::Model('Mobile/Action')->deleteDashboardItem($this->getParam('item_id')))
        {
            return $this->toJson(array(
                'error' => false,
                'deleted' => true
            ));
        }

        return $this->toJson(array('error' => true, 'deleted' => true));
    }

    public function getDashboardEditForm()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->output(null);
        }

        $oMobile = Linko::Model('Mobile');

        return $this->output(Linko::Template()->getTemplate('mobile/block/admincp/_item-form', array(
            'aDashboard' => $oMobile->getDashboardItem((int)$this->getParam('item_id')),
            'aModules' => $oMobile->getModules(),
        )));
    }

    public function updateDashboardOrder()
    {
        if(Linko::Model('User/Auth')->getUserBy('role_id') != CMS::USER_ROLE_ADMIN)
        {
            return $this->toJson(array('error' => true, 'order' => array()));
        }

        $aOrder = $this->getParam('order');

        // I am handling the order a little differently here.

        if(Linko::Model('Mobile/Action')->updateDashboardOrder($aOrder))
        {
            $this->toJson(array('error' => false, 'order' => $aOrder));
        }
    }
}