<?php

defined('LINKO') or exit();

/**
 * Core File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

Linko::Config()->set('session.storage', 'default');

Linko::Config()->set('cookie.domain', Linko::Module()->getSetting('cookie.domain'));

Linko::Config()->set('cookie.prefix', Linko::Module()->getSetting('cookie.prefix'));

Linko::Config()->set('session.prefix', Linko::Module()->getSetting('cookie.prefix'));

Linko::Config()->set('cookie.path', Linko::Module()->getSetting('cookie.path'));