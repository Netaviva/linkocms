<?php

defined('LINKO') or exit();

/**
 * @author Morrison Laju
 * @package linkocms
 * @subpackage stat : plugin - admincp.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Stat_Plugin_Admincp
{
    public function con_dashboard()
    {
        Linko::Template()->setScript(array(
            'jquery.jqplot.js', 
            'jqplot.dateAxisRenderer.min.js',
            'jqplot.highlighter.min.js'
        ), 'module_stat');
        
        Linko::Template()->setStyle(array('jquery.jqplot.css', 'stat-dashboard.css'), 'module_stat');
        
        Linko::Model('Admincp')->addDashboard('dashboard_top', array(
            'title' => 'Stat Counter',
            'content' => 'stat/dashboard',
            'type' => 'block'
        ));
    }
}

?>