<?php

$app->group('/hello', function () use ($app) {
	// documented
	$app->post('/', 'hello_post');
});


function hello_post() {
	$app = \Slim\Slim::getInstance();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;
	$stmt = $db->prepare("update users set last_hello = NOW() where pk_user_id = :user_id");
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	serve_nothing($app, 200);
}
