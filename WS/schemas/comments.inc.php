<?php

$app->group('/comments', function () use ($app) {
	// documented
	$app->get('/:post_id(/:ts)','comments_get')->conditions(array(
			'post_id'=>'\d{1,}'
		));
});



function comments_get($post_id, $ts=null) {
	$app = \Slim\Slim::getInstance();
	S3_init($app);
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;
	//el("timestamp ".$ts);
	if ($ts === null) {
		$stmt = $db->prepare("select * from comments_get(:post_id)");
	} else {
		$stmt = $db->prepare("select * from comments_get(:post_id, :until_ts)");
		$stmt->bindParam(':until_ts', $ts, PDO::PARAM_STR);
	}
	$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($res as &$c) {
		$c['url_profile'] = S3_resolve_filename($app, $c['filename_profile']);
	}
	//error_log(print_r($res,1));
	serve_json($app, $res, 200);
}
