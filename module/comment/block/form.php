<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage comment : block - form.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Comment_Block_Form extends Linko_Controller
{
    public function main()
    {
        Linko::Template()->setVars(array(
            'bIsUser' => Linko::Model('User/Auth')->isUser(),
	        'iItemId' => $this->getParam('iItemId'),
	        'sModule' => $this->getParam('sModule'),
        ));
    }
}

?>