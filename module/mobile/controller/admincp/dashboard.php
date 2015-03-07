<?php defined('LINKO') or exit;

/**
 * @package Mobile
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class Mobile_Controller_Admincp_Dashboard extends Linko_Controller
{
    public function main()
    {
        /**
         * @var $oMobile Mobile_Model_Mobile
         */
        $oMobile = Linko::Model('Mobile');

        Linko::Template()->setScript('admin.js', 'module_mobile');
        Linko::Template()->setStyle('admin.css', 'module_mobile');

        Linko::Template()->setTitle('Dashboard Manager');
        Linko::Template()->setBreadcrumbTitle('Dashboard Manager');

        Linko::Template()->setVars(array(
            'aModules' => $oMobile->getModules(),
            'aDashboardItems' => $oMobile->getDashboardItems()
        ));
    }
}