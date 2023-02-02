<?php
function new_redis($db) {
    $r = new Redis();
    $r->connect(TEMPR_STORE_HOST);
    $r->select($db);
    return $r;
}
