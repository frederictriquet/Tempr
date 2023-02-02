<?php

require_once('tools/events.inc.php');

$app->group('/like', function () use ($app) {
	// documented
	$app->post('/(:post_id)/(:tag_num)','like_post')->conditions(array(
			'post_id'=>'\d{1,}',
			'tag_num'=>'\d{1}'
	));
});


function like_post($post_id, $tag_num) {
	$app = \Slim\Slim::getInstance();
	$post_id = (int)$post_id;
	$tag_num = (int)$tag_num;

	$body = $app->request->getBody();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;
	$stmt = $db->prepare('select * from htag_like(:user_id, :post_id, :tag_num)');
	$stmt->bindValue(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
	$stmt->bindValue(':tag_num', $tag_num, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_NUM);
	//error_log(print_r($res,1));
	if ((count($res)==1) && ($res[0][0] > 0)) {
	    process_event($app, 'like', (int)$pk_user_id, (int)$post_id);
	}
	serve_json($app,$res, 200);
}


