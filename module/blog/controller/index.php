<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage blog : index.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Blog_Controller_Index extends Linko_Controller
{
	public function main()
	{
        $sArchive = $this->getParam('archive');
        
        $sSlug = $this->getParam('slug');  
       
		$iPage = max($this->getParam('page'), 1);
                
        
        $iLimit = (int)Linko::Module()->getSetting('blog.post_per_page');
        
		list($iTotal, $aPosts) = Linko::Model('Blog/Browse')
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

		Linko::Template()
			->setVars(array(
					'aPosts' => $aPosts,
					'iTotal' => $iTotal,
			), $this);
	}
}

?>