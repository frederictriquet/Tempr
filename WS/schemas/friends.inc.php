<?php

$app->group('/friends', function () use ($app) {
	// not documented
	$app->get('/:user_id(/:start)', 'friends_get')->conditions(array(
			'user_id'=>'\d{1,}',
			'start'=>'\d{1,}'
	));
});

function friends_get($other_user_id, $start = 0) {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$pk_user_id = $app->pk_user_id;
	$db = db_connect();

	// TODO check if user_id and $body['pk_user_id'] are friends
	//$stmt = $db->prepare('select fk_user_id2 from friendships where fk_user_id1=:user_id');
	$stmt = $db->prepare("select * from profile_friends_of(:other_user_id,:start)");
	$stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':start', $start, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

	serve_json($app,$res,200);
}

