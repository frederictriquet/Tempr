<?php

// if you modify this file
// don't forget to modify
// Vagrant/ansible/roles/ws/templates/db.inc.php.j2
// accordingly

define('TEMPR_DB_HOST','localhost');
define('TEMPR_DB_NAME','tempr');
define('TEMPR_DB_USER','tempr');
define('TEMPR_DB_PASS','vierge');

define('TEMPR_STORE_HOST', 'localhost');
define('TEMPR_MQ_HOST', 'localhost');

define('TEMPR_STORE_EVENTS_AND_PUSH', 0);
define('TEMPR_STORE_AUTH_DB', 1);
define('TEMPR_STORE_ANTI_HAMMERING_DB', 2);
define('TEMPR_STORE_TRENDS_DB', 3);
define('TEMPR_STORE_CONFIRMPHONE_DB', 4);
define('TEMPR_STORE_POSTSMS_DB', 5);
