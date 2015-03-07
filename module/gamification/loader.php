<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage user : loader
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

Linko::Template()->setPathAlias('gamification_image', array(Linko::Model('gamification/Helper/icon'), 'getIcon'));