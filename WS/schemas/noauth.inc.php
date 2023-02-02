<?php
require_once('tools/events.inc.php');
require_once('conf/fb.inc.php');
require_once('tools/hammering.inc.php');

$app->group('/noauth', function () use ($app) {
	// documented
	$app->put('/user/', 'user_put');
	// documented
	$app->put('/user/fb/', 'user_fb_put');
	// on purpose not documented
	$app->delete('/user/:id', 'admin_user_delete');
	// documented
	$app->get('/lostpass/:email', 'lostpass_get');
});


function user_put() {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();//$app->environment()['slim.input'];
	//elp($_SERVER);
	hammering_prevent_userput($app, $_SERVER['REMOTE_ADDR']);
	$db = db_connect();
	$stmt = $db->prepare('select * from user_create(:login, :email, :firstname, :lastname, :password)');

	$email = substr($body['email'], 0, 120);
	$firstname = substr($body['firstname'], 0, 120);
	$lastname = substr($body['lastname'], 0, 120);
	$login = make_login($firstname, $lastname);

	if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
		serve_error($app, 'Incorrect e-mail address', 403);
		return;
	}
	if (! password_is_safe($body['password'])) {
		serve_error($app, 'Password not safe', 403);
		return;
	}

	$hash = password_hash($body['password'], PASSWORD_DEFAULT);
    //elp($body);
	$stmt->bindParam(':login', $login, PDO::PARAM_STR);
	$stmt->bindParam(':email', $email, PDO::PARAM_STR);
	$stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
	$stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
	$stmt->bindParam(':password', $hash, PDO::PARAM_STR);

	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//elp($res);
	if ($res[0]['created'] === True) {
	    process_event($app, 'new_user', (int)$res[0]['pk_user_id']);
	    $stmt = $db->prepare('select count(*) from users');
	    $stmt->execute();
	    $nb = $stmt->fetchAll(PDO::FETCH_NUM);
	    send_mail_new_user($login, $nb[0][0]);
		serve_json($app, $res[0], 201);
	} else {
		serve_error($app, 'E-mail address already used', 409);
	}
}


function user_fb_put() {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();//$app->environment()['slim.input'];

	$token = $body['token'];

	$fb = new Facebook\Facebook([
			'app_id' => FB_APP_ID,
			'app_secret' => FB_APP_SECRET,
			'default_graph_version' => 'v2.6',
			]);
	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get('/me?fields=id,last_name,first_name', $token);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		error_log('Graph returned an error: ' . $e->getMessage());
		serve_error($app, 'Graph returned an error: '.$e->getMessage(), 500);
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		error_log('Facebook SDK returned an error: ' . $e->getMessage());
		serve_error($app, 'Facebook SDK returned an error: '.$e->getMessage(), 500);
	}

	$user = $response->getGraphUser();

	$db = db_connect();
	$stmt = $db->prepare('select * from user_fb_create(:login, :firstname, :lastname, :facebook_id)');

	$firstname = substr($user['first_name'], 0, 120);
	$lastname = substr($user['last_name'], 0, 120);
	$login = make_login($firstname, $lastname);
	$facebook_id = $user['id'];

	$stmt->bindParam(':login', $login, PDO::PARAM_STR);
	$stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
	$stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
	$stmt->bindParam(':facebook_id', $facebook_id, PDO::PARAM_STR);

	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if ($res[0]['created'] === True) {
		$status = 201;
		$r = $res[0];
	    process_event($app, 'new_user', (int)$r['pk_user_id']);
		$r['firstname'] = $firstname;
		$r['lastname'] = $lastname;
		$r['facebook_id'] = $facebook_id;
		process_event($app, 'fb_connected', $r['pk_user_id']);
	    $stmt = $db->prepare('select count(*) from users');
	    $stmt->execute();
	    $nb = $stmt->fetchAll(PDO::FETCH_NUM);
	    send_mail_new_user($login, $nb[0][0]);
		serve_json($app, $r, 201);
	} else {
		serve_error($app, 'Facebook account already used', 409);
	}
}


function admin_user_delete($id) {
	//error_log("delete");
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();//$app->environment()['slim.input'];
	$login = $app->request->headers('PHP_AUTH_USER');
	$password = $app->request->headers('PHP_AUTH_PW');
	if ($login === 'TemprAdmin' && $password === 'AdminPassword') { // FIXME SECURITY
		$db = db_connect();
		$stmt = $db->prepare('delete from users where pk_user_id = :userid');
		$stmt->bindParam(':userid', $id, PDO::PARAM_INT);
		$stmt->execute();

		serve_nothing($app, 204);
	} else {
		serve_error($app, 'Should not happen', 401);
	}
}



function lostpass_get($email) {
	$app = \Slim\Slim::getInstance();
    hammering_prevent_lostpass_same_ip($app, $_SERVER['REMOTE_ADDR']);
    hammering_prevent_lostpass_same_account($app, $email);
	$db = db_connect();
	$stmt = $db->prepare('select pk_user_id from users where email=:email limit 1');
	$stmt->bindParam(':email', $email, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($res) === 1) {
		$user_id = (int)$res[0]['pk_user_id'];
		$token = get_token();
		$stmt = $db->prepare('insert into password_resets(fk_user_id,token) values(:user_id,:token)');
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindParam(':token', $token, PDO::PARAM_STR);
		$stmt->execute();
		send_mail_reset_pass($email, $token);
		serve_nothing($app, 200);
	} else {
	    serve_error($app, 'no such e-mail address', 404);
	}
}



