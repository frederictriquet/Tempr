<?php

require_once('tools/events.inc.php');

// TODO user conditions
$app->group('/friendship', function () use ($app) {
	// documented
	$app->get('/', 'friendship_get');
	// documented
	$app->post('/:user_id', 'friendship_post')->conditions(array('userid'=>'\d{1,}'));
	// documented
	$app->delete('/:user_id', 'friendship_delete')->conditions(array('userid'=>'\d{1,}'));
	// documented
	$app->post('/refuse/:user_id', 'friendship_refuse_post')->conditions(array('userid'=>'\d{1,}'));
});

function friendship_get() {
	$app = \Slim\Slim::getInstance();
	$db = db_connect();
	$id = $app->pk_user_id;
	$stmt = $db->prepare('select * from friendship_requests_get(:user_id)');
	$stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

	serve_json($app,$res, 200);
}




function friendship_post($other_user_id) {
	$app = \Slim\Slim::getInstance();
	$app->db = db_connect();
	$res = request_friendship($app, $other_user_id);
	serve_json($app,$res, 200);
}



function friendship_delete($other_user_id) {
	$other_user_id = (int)$other_user_id;
	$app = \Slim\Slim::getInstance();
	$db = db_connect();
	$id = $app->pk_user_id;
	$stmt = $db->prepare('select * from friendship_delete(:user_id1,:user_id2)');
	$stmt->bindParam(':user_id1', $id, PDO::PARAM_INT);
	$stmt->bindParam(':user_id2', $other_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($res) == 1) {
        process_event($app, $res[0]['event_type'], $res[0]['event_data'], $res[0]['event_data2']);
    }
	//error_log(print_r($res,1));
	serve_json($app,$res, 200);
}


function friendship_refuse_post($other_user_id) {
    $app = \Slim\Slim::getInstance();
    $db = db_connect();
    $id = $app->pk_user_id;
    $stmt = $db->prepare('select * from friendship_request_refuse(:user_id1,:user_id2)');
    $stmt->bindParam(':user_id1', $id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id2', $other_user_id, PDO::PARAM_INT);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($res) == 1) {
        process_event($app, $res[0]['event_type'], $res[0]['event_data'], $res[0]['event_data2']);
    }
    serve_json($app,$res, 200);
}

