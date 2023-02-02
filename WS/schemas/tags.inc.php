<?php

$app->group('/tags', function () use ($app) {
	// not documented
	$app->get('/:userid', 'tags_get')->conditions(array('userid'=>'\d{1,}'));
});


function tags_get($user_id) {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$db = db_connect();

	$stmt = $db->prepare("select * from htags_suggest(:_user_id)");
	$stmt->bindParam(':_user_id', $user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = array();
	$tmp = $stmt->fetchAll(PDO::FETCH_NUM);
	foreach ($tmp as $t)
		$res[] = $t[0];
	serve_json($app, $res, 200);
}
