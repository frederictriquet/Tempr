<?php

$app->group('/unlike', function () use ($app) {
	// documented
	$app->post('/(:post_id)/(:tag_num)','unlike_post')->conditions(array(
			'post_id'=>'\d{1,}',
			'tag_num'=>'\d{1}'
	));
});


function unlike_post($post_id, $tag_num) {
	$app = \Slim\Slim::getInstance();

	$body = $app->request->getBody();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;
	$stmt = $db->prepare('select * from htag_unlike(:user_id, :post_id, :tag_num)');
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
	$stmt->bindParam(':tag_num', $tag_num, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//error_log(print_r($res,1));
	serve_json($app,$res, 200);
}


