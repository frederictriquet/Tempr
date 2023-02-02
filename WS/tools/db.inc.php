<?php
require 'conf/db.inc.php';

function db_connect() {
	return new PDO('pgsql:dbname='.TEMPR_DB_NAME.'; host='.TEMPR_DB_HOST.'; user='.TEMPR_DB_USER.'; password='.TEMPR_DB_PASS);
}
