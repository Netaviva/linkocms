<?php

defined('LINKO') or exit();

/**
 * @author Morrison Laju
 * @package linkocms
 * @subpackage stat : block - dashboard.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Stat_Block_Dashboard extends Linko_Controller
{
    public function main()
    {
        $aChart = array();
        
        $aChart = Linko::Model('stat')->getStatChartData();
        $iTotalHits = Linko::Model('stat')->getTotalHits();
        $iUniqueHits = Linko::Model('stat')->getUniqueHits();
        $iTotalUsers = Linko::Model('User')->getTotalUsers();

        Linko::Template()->setVars(array(
            'aChart' => $aChart,
            'sChart' => Linko::Json()->encode($aChart),
            'iTotalHits' => $iTotalHits,
            'iUniqueHits' => $iUniqueHits,
            'iTotalUsers' => $iTotalUsers,
        ));
    }
}

?>