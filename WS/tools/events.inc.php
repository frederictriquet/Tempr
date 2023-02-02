<?php
require_once('redis.inc.php');

function process_event(&$app, $event_type, $event_data, $event_data2=null) {
    elp('process event '.$event_type.' '.$event_data.' '.$event_data2);
    $obj = null;
    if (gettype($event_data) !== 'integer') elp('APPEL A PROCESS EVENT AVEC UN ID NON ENTIER '.gettype($event_data));
    switch ($event_type) {
        case 'N/A': break;
        case 'friendship_denied': break;
        case 'already_friends': break;
        case 'friendship_request':
            $obj = (object)['type'=>'friendship_request', 'from_id'=>$event_data, 'to_id'=>$event_data2];
            break;
        case 'friendship_acceptance':
            $obj = (object)['type'=>'friendship_acceptance', 'from_id'=>$event_data, 'to_id'=>$event_data2];
            break;
        case 'friendship_removal':
            $obj = (object)['type'=>'friendship_removal', 'from_id'=>$event_data, 'to_id'=>$event_data2];
            break;
        case 'post':
            $obj = (object)['type'=>'post', 'post_id'=>$event_data];
            break;
        case 'pending_post': break;
        case 'post_delete':
            $obj = (object)['type'=>'post_delete', 'post_id'=>$event_data];
            break;
        case 'user_delete':
            $obj = (object)['type'=>'user_delete', 'to_id'=>$event_data];
            break;
        case 'comment':
            $obj = (object)['type'=>'comment', 'from_id'=>$event_data, 'post_id'=>$event_data2];
            break;
        case 'like':
            $obj = (object)['type'=>'like', 'from_id'=>$event_data, 'post_id'=>$event_data2];
            break;
        case 'profile_updated':
            $obj = (object)['type'=>'profile_updated', 'from_id'=>$event_data];
            break;
        case 'background_updated':
            $obj = (object)['type'=>'background_updated', 'from_id'=>$event_data];
            break;
        case 'stats_updated':
            $obj = (object)['type'=>'stats_updated', 'to_id'=>$event_data];
            break;
        case 'sysmsg': break;
        case 'phone_confirmed':
            $obj = (object)['type'=>'phone_confirmed', 'from_id'=>$event_data];
            break;
        case 'fb_connected':
            $obj = (object)['type'=>'fb_connected', 'from_id'=>$event_data];
            break;
        case 'user_deleted':
            $obj = (object)['type'=>'user_deleted', 'from_id'=>$event_data];
            break;
        case 'post_by_phone':
            $obj = (object)['type'=>'post_by_phone', 'post_id'=>$event_data];
            break;
        case 'new_user':
            $obj = (object)['type'=>'new_user', 'from_id'=>$event_data];
            break;
    }
    if ($obj != null)
        send_to_rabbit('events', $obj);
}


/* for a given user, store into redis :
 * the date of the most recent event a user has retrieved
 * this function is called through GET /events/ and GET /events/up/...
 * using this timestamp we are enabled to know if we have to notify the user
 * "you have unread events" 
 */
function update_recent_event_update($pk_user_id, $new_ts) {
    $r = new_redis(TEMPR_STORE_EVENTS_AND_PUSH);
    $k = 'e'.$pk_user_id;
    $ts = $r->get($k);
    //elp("current most recent event for ".$pk_user_id." ".$ts);
    if ($new_ts > $ts) {
        //elp("updating most recent event for ".$pk_user_id." ".$new_ts);
        $r->set($k, $new_ts);
    }
}

function events_exist(&$db, $pk_user_id) {
    $r = new_redis(TEMPR_STORE_EVENTS_AND_PUSH);
    $k = 'e'.$pk_user_id;
    if ($r->exists($k)) {
        $ts = $r->get($k);
    } else {
        $ts = "1970-01-01 00:00:00";
    }
    //elp("checking events for ".$pk_user_id." ".$ts);
    $stmt = $db->prepare("select * from events_exist(:user_id, :ts)");
    $stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
    $stmt->bindParam(':ts', $ts);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM)[0][0];
}
