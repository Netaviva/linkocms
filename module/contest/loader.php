<?php

define('DIR_CONTEST_LOGO', DIR_UPLOAD . 'contest' . DS);

Linko::Template()->setPathAlias('contest_image', array(Linko::Model('Contest/Helper/Photo'), 'getPhoto'));