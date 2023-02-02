<?php

//require_once('tools/random.inc.php');
//require_once('tools/misc.inc.php');
require_once('tools/events.inc.php');


$app->group('/comment', function () use ($app) {
	// documented
	$app->post('/:post_id','comment_post')->conditions(array(
			'post_id'=>'\d{1,}'
		));
	// documented
	$app->delete('/:comment_id','comment_delete')->conditions(array(
			'comment_id'=>'\d{1,}'
		));
});


function comment_post($post_id) {
    $post_id = (int)$post_id;
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$app->db = db_connect();
	$pk_user_id = $app->pk_user_id;

	$comment_body = get_mandatory_value('body', $body);

	$stmt = $app->db->prepare("select * from comment_create(:post_id,:from_user_id,:body)");
	$stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
	$stmt->bindValue(':from_user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindValue(':body', $comment_body, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if ((count($res)==1) && ($res[0]['comment_create'] > 0)) {
	    process_event($app, 'comment', $pk_user_id, $post_id);
		serve_nothing($app, 200);
	} else {
		serve_error($app, 'Comment forbidden', 403);
	}
}


function comment_delete($comment_id) {
    $comment_id = (int)$comment_id;
	$app = \Slim\Slim::getInstance();
	$app->db = db_connect();
	$pk_user_id = $app->pk_user_id;

	$stmt = $app->db->prepare("select * from comment_delete(:comment_id,:user_id)");
	$stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if ((count($res)==1) && ($res[0]['comment_delete'] > 0)) {
		serve_nothing($app, 200);
	} else {
		serve_error($app, 'Delete comment forbidden', 403);
	}
}
