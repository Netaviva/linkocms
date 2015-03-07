<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage admincp : model - task.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Admincp_Model_Task extends Linko_Model
{
    public function install()
    {
        Linko::Model('User/Role/Action')->addSetting('admincp', array(
            'admincp.can_access_admincp' => 0
        ));

        Linko::Model('User/Role/Action')->setSetting(CMS::USER_ROLE_ADMIN, array(
            'admincp.can_access_admincp' => 1
        ));
    }
}

?>