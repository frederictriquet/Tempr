<?php
require_once('redis.inc.php');

define('HAMMERING_USERPUT', 10);
define('HAMMERING_BF_SAME_IP', 10);
define('HAMMERING_BF_SAME_ACCOUNT', 10);
define('HAMMERING_LP_SAME_IP', 10);
define('HAMMERING_LP_SAME_ACCOUNT', 10);

function redis_test_and_set($k, $duration) {
    $r = new_redis(TEMPR_STORE_ANTI_HAMMERING_DB);
    $res = $r->exists($k);
    $r->set($k, '', $duration);
    $r->close();
    return $res;
}

function hammering_prevent_(&$app, $k, $duration, $sleep, $msg) {
    return; // REMOVE ME FOR ANTI-HAMMERING SECURITY
    if (redis_test_and_set($k, $duration)) {
        sleep($sleep);
        elp('HAMMERING '.$msg);
        serve_error($app, 'Too many requests', 409);
    }
}

function hammering_prevent_userput(&$app, $ip) {
    hammering_prevent_($app, 'up_'.$ip, HAMMERING_USERPUT, 5, $ip.' userput');
}

function hammering_prevent_bf_same_ip(&$app, $ip) {
    hammering_prevent_($app, 'bf_'.$ip, HAMMERING_BF_SAME_IP, 5, $ip.' bf');
}

function hammering_prevent_bf_same_account(&$app, $email) {
    hammering_prevent_($app, 'bf_'.$email, HAMMERING_BF_SAME_ACCOUNT, 5, $email.' bf');
}

function hammering_prevent_lostpass_same_ip($app, $ip) {
    hammering_prevent_($app, 'lp_'.$ip, HAMMERING_LP_SAME_IP, 5, $ip.' lostpass');
}

function hammering_prevent_lostpass_same_account($app, $email) {
    hammering_prevent_($app, 'lp_'.$email, HAMMERING_LP_SAME_ACCOUNT, 5, $email.' lostpass');
}

