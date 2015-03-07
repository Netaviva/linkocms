<?php

define('DIR_MODULE', Linko::Config()->get('dir.module'));

define('DIR_STORAGE', Linko::Config()->get('dir.storage'));

define('DIR_UPLOAD', DIR_STORAGE . 'upload' . DS);

define('DIR_TMP', DIR_STORAGE . 'tmp' . DS);

define('DIR_LOG', DIR_STORAGE . 'log' . DS);