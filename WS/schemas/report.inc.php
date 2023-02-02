<?php

//require_once('tools/random.inc.php');
//require_once('tools/misc.inc.php');


$app->group('/report', function () use ($app) {
	// documented
	$app->post('/post/:post_id','report_post_post')->conditions(array(
			'post_id'=>'\d{1,}'
		));
	// documented
	$app->post('/comment/:comment_id','report_comment_post')->conditions(array(
			'comment_id'=>'\d{1,}'
		));
});


function report_post_post($post_id) {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$app->db = db_connect();
	$pk_user_id = $app->pk_user_id;

	$stmt = $app->db->prepare("select * from report_post(:post_id,:user_id)");
	$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	serve_nothing($app, 200);
}


function report_comment_post($comment_id) {
	$app = \Slim\Slim::getInstance();
	$app->db = db_connect();
	$pk_user_id = $app->pk_user_id;

	$stmt = $app->db->prepare("select * from report_comment(:comment_id,:user_id)");
	$stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	serve_nothing($app, 200);
}
