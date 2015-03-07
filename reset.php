<?php

mysql_connect('localhost', 'morrelinko', '123456');
mysql_select_db('linkocms');

mysql_query('DROP DATABASE linkocms');
mysql_query('CREATE DATABASE linkocms');

unset($_SESSION);

echo 'done';