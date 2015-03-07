<?php

define('DIR_BLOG_PHOTO', DIR_UPLOAD . 'blog' . DS);

Linko::Template()->setPathAlias('blog_image', array(Linko::Model('Blog/Helper/Photo'), 'getPhoto'));