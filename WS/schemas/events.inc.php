<?php

require_once('tools/events.inc.php');

$app->group('/events', function () use ($app) {
	// documented
	$app->get('/(down/:ts)', 'events_get');
	// documented
	$app->get('/up/:ts', 'events_up_get');
});


function events_get($ts=null) {
	$app = \Slim\Slim::getInstance();
	S3_init($app);
	$pk_user_id = $app->pk_user_id;
	$db = db_connect();
	if ($ts === null) {
		$stmt = $db->prepare('select * from events_get(:user_id)');
	} else {
		$stmt = $db->prepare('select * from events_get(:user_id,:until_ts)');
		$stmt->bindParam(':until_ts', $ts, PDO::PARAM_STR);
	}
	
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($res) > 0) {
        if ($ts === null)
            update_recent_event_update($pk_user_id, $res[0]['creation_ts']);
    	foreach ($res as &$e) {
    	    $e['from_url_profile'] = S3_resolve_filename($app, $e['from_profile']);
    	    unset($e['from_profile']);
    	    $e['to_url_profile'] = S3_resolve_filename($app, $e['to_profile']);
    	    unset($e['to_url_profile']);
    	}
    }

	serve_json($app, $res, 200);
}


function events_up_get($ts) {
	$app = \Slim\Slim::getInstance();
	S3_init($app);
	$pk_user_id = $app->pk_user_id;
	$db = db_connect();
	$stmt = $db->prepare('select * from events_get_up(:user_id,:from_ts)');
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':from_ts', $ts, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($res) > 0) {
        update_recent_event_update($pk_user_id, $res[0]['creation_ts']);
    	foreach ($res as &$e) {
    	    $e['from_url_profile'] = S3_resolve_filename($app, $e['from_profile']);
    	    unset($e['from_profile']);
    	    $e['to_url_profile'] = S3_resolve_filename($app, $e['to_profile']);
    	    unset($e['to_url_profile']);
    	}
    }
	serve_json($app, $res, 200);
}

