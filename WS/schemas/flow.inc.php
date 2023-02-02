<?php

$app->group('/flow', function () use ($app) {
	// documented
	$app->get('/(:start)','flow_get')->conditions(array(
			'start'=>'\d{1,}'
		));
	// TODO WHEN GET /flow/:start is not used any more remove it AND change '/down/(:ts)' to '/(down/:ts)'
	// documented
	$app->get('/down/(:ts)','flow_down_get');
	// documented
	$app->get('/up/:ts','flow_up_get');
});


function flow_get($start = 0) {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->pk_user_id;
	$db = db_connect();

	$start = (int)$start;

	$stmt = $db->prepare('select * from flow_posts_get(:user_id, :start)');
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':start', $start, PDO::PARAM_INT);
	$res = retrieve_temps($app, $db, $stmt);

	serve_json($app, $res, 200);
}



function flow_down_get($ts = null) {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->pk_user_id;
	$db = db_connect();
	//el($pk_user_id);
	if ($ts !== null) {
		$stmt = $db->prepare('select * from flow_posts_down_get(:user_id, :before)');
		$stmt->bindParam(':before', $ts, PDO::PARAM_STR);
	} else {
		$stmt = $db->prepare('select * from flow_posts_down_get(:user_id)');
	}
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$res = retrieve_temps($app, $db, $stmt);

	serve_json($app, $res, 200);
}

function flow_up_get($ts) {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->pk_user_id;
	$db = db_connect();

	$stmt = $db->prepare('select * from flow_posts_up_get(:user_id, :after)');
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':after', $ts, PDO::PARAM_STR);
	$res = retrieve_temps($app, $db, $stmt);

	serve_json($app, $res, 200);
}

