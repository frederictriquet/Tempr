<?php
require_once('redis.inc.php');

define('TEMPR_EXPIRE',86400*7*2);

function user_login($user_id, &$desired_token, &$desired_refresh_token) {
    $r = new_redis(TEMPR_STORE_AUTH_DB);
    if ($r->exists($user_id)) {
        $desired_token = $r->get($user_id);
        $desired_refresh_token = $r->get($user_id.':rt');
    } else {
        $r->set($user_id, $desired_token);
        $r->set($user_id.':rt', $desired_refresh_token);
        $r->set($desired_token, $user_id);
        $r->set($desired_refresh_token, $user_id);
    }
    $r->expire($user_id, TEMPR_EXPIRE);
    $r->expire($desired_token, TEMPR_EXPIRE);
    $r->close();
}

function user_check_token_and_postpone($token) {
    $r = new_redis(TEMPR_STORE_AUTH_DB);
    $user_id = $r->get($token);
    if ($user_id !== null) {
        $r->expire($user_id, TEMPR_EXPIRE);
        $r->expire($token, TEMPR_EXPIRE);
    } else
        $user_id = 0;
    $r->close();
    return $user_id;
}

function user_logout() {

}

function user_renew_token($refresh_token, $desired_token) {
    $r = new_redis(TEMPR_STORE_AUTH_DB);
    $user_id = $r->get($refresh_token);
    $found = ($user_id !== null);
    if ($found) {
        $r->setex($user_id, TEMPR_EXPIRE, $desired_token);
        $r->setex($desired_token, TEMPR_EXPIRE, $user_id);
    }
    $r->close();
    return $found;
}
