<?php


$app->group('/event', function () use ($app) {
	// documented
	$app->delete('/:id', 'event_delete')->conditions(array(
			'id'=>'\d{1,}'
		));
});


function event_delete($id) {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->pk_user_id;
	$id = (int)$id;
	$db = db_connect();
	$stmt = $db->prepare('delete from events where to_fk_user_id = :user_id and pk_event_id = :id');
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

	serve_json($app, $res, 200);
}


