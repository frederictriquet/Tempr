<?php

$app->group('/logout', function () use ($app) {
	// documented
	//$app->get('/', 'logout_get');
	// documented
	$app->post('/', 'logout_post');
});

function logout_get() {
	//error_log("Logout");
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();

	$db = db_connect();
	$stmt = $db->prepare('select * from user_logout(:token)');
	$token = $app->request->headers->get('Authorization');
	$stmt->bindParam(':token', $token, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($res) === 1) {
		$a = (object)['logged_out'=>$res[0]['user_logout']];
		serve_json($app, $a, 200);
	} else {
		serve_error($app, 'Logout failed', 401);
	}
}

function logout_post() {
    //error_log("Logout");
    $app = \Slim\Slim::getInstance();
    $body = $app->request->getBody();
    $db = db_connect();

    $iosdevice = get_optional_value('iosdevice', $body, NULL);
    if ($iosdevice !== NULL) {
        $stmt = $db->prepare('select user_remove_iosdevice(:user_id,:iosdevice)');
        $stmt->bindValue(':user_id', $app->pk_user_id, PDO::PARAM_INT);
        $stmt->bindValue(':iosdevice', $iosdevice, PDO::PARAM_STR);
        $stmt->execute();
    }
    serve_nothing($app, 204);
}

