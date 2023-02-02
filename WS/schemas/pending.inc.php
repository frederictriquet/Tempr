<?php

require_once('tools/events.inc.php');


$app->group('/pending', function () use ($app) {
	// documented
	$app->post('/:token', 'pending_post');
	// documented
	$app->delete('/:token', 'pending_delete');
});


// CONFIRMTOKEN: VALIDATE THE UPDATE, REMOVE THE OLD PROFILE PICTURE
function pending_post($token) {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->pk_user_id;

	$db = db_connect();
	$stmt = $db->prepare('select * from pending_upload_confirm(:token,:user_id)');
	$stmt->bindParam(':token', $token, PDO::PARAM_STR);
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($res) == 1) {
	    process_event($app, $res[0]['event_type'], $res[0]['event_data'], $res[0]['event_data2']);
	}
	
	serve_nothing($app, 200);
}

// TODO DEAL WITH THE CONFIRMTOKEN: INVALIDATE THE UPDATE
function pending_delete($token) {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->pk_user_id;

	$db = db_connect();
	$stmt = $db->prepare('select * from pending_upload_abort(:token,:user_id)');
	$stmt->bindParam(':token', $token, PDO::PARAM_STR);
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	//$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

	serve_nothing($app, 200);
}


