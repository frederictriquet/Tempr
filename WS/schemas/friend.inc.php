<?php

$app->group('/friend', function () use ($app) {
	// documented
	$app->delete('/:user_id', 'friends_delete')->conditions(array(
			'user_id'=>'\d{1,}'
		));
});

function friend_delete($user_id) {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;
	// TODO check if user_id and $body['pk_user_id'] are friends
	$stmt = $db->prepare('select friendship_delete(:user_id, :user_id2)');
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':user_id2', $user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

	serve_json($app,$res,200);
}


