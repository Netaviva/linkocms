<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage contest : index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Contest_Controller_Index extends Linko_Controller {

    public function main() {
        
        $sArchive = $this->getParam('archive');

        $sSlug = $this->getParam('slug');

        $iPage = max($this->getParam('page'), 1);

        $iLimit = (int)Linko::Module()->getSetting('contest.contest_per_page');

        list($iTotal, $aContests) = Linko::Model('Contest/Browse')
                ->archive($sArchive, $sSlug)
                ->approved(true)
                ->page($iPage)
                ->limit($iLimit)
                ->get();

        $aPager = array(
            'total_items' => $iTotal,
            'current_page' => $iPage,
            'rows_per_page' => $iLimit,
        );
        
       if($sArchive)
        {
            $aPager['route_param'] = array(
                'archive' => $sArchive,
                'slug' => $sSlug
            );
        }
        
        Linko::Pager()->set($aPager);                
        
         Linko::Template()->setBreadcrumb(array(
                    Lang::t('contest.sport') => Linko::Url()->make('contest.index'),
                        ), Lang::t('contest.sport'))
                ->setStyle(array('contest.css'), 'module_contest')
                ->setScript(array('select.js', 'contest.js', 'jquery.countdown.js'), 'module_contest')
                ->setVars(array(
                    'aContests' => $aContests,
                    'iTotal' => $iTotal,
                        ), $this);
    }

}

?>