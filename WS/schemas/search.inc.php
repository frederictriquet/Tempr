<?php

$app->group('/search', function () use ($app) {
	// documented
	$app->get('/email/:email', 'search_email_get');
	// documented
	$app->get('/phone/:phone', 'search_phone_get');
	// documented
	$app->post('/phones/', 'search_phones_post');
	// documented
	$app->post('/fb/', 'search_fb_post');
});

function search_email_get($email) {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$db = db_connect();

	$stmt = $db->prepare('select pk_user_id, login, firstname, lastname, filename_profile from view_profiles where email=:email and email_confirmed limit 1');
	//$stmt = $db->prepare('select pk_user_id, login, firstname, lastname, filename_profile from view_profiles where email=:email limit 1');
	$stmt->bindParam(':email', $email, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

	process_result($app, $res);
}

function search_phone_get($phone) {
	//error_log($phone);
	$app = \Slim\Slim::getInstance();
	$db = db_connect();

	$stmt = $db->prepare('select pk_user_id, login, firstname, lastname, filename_profile from view_profiles where phone=:phone and phone_confirmed limit 1');
	//$stmt = $db->prepare('select pk_user_id, login, firstname, lastname, filename_profile from view_profiles where phone=:phone limit 1');
	$stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

	process_result($app, $res);
}

function search_phones_post() {
	//error_log($phone);
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	//elp($body);
	if (! is_array($body))
		serve_error($app, 'Incorrect body, should be an array', 400);

	$db = db_connect();

	$stmt = $db->prepare('select pk_user_id, login, firstname, lastname, filename_profile from view_profiles where phone=:phone and phone_confirmed limit 1');
	//$stmt = $db->prepare('select pk_user_id, login, firstname, lastname, filename_profile from view_profiles where phone=:phone limit 1');
	$res = array();
	foreach ($body as $n) {
		// TODO if (phone_number_is_valid($n))
		$stmt->execute(array(':phone' => $n));
		$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($r) === 1)
			$res[] = $r[0];
	}

	process_results($app, $res);
}


function search_fb_post() {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	//elp($body);
	if (! is_array($body))
		serve_error($app, 'Incorrect body, should be an array', 400);

	$db = db_connect();

	$stmt = $db->prepare('select pk_user_id, login, firstname, lastname, filename_profile from view_profiles where facebook_id=:fb limit 1');
	$res = array();
	foreach ($body as $n) {
		$stmt->execute(array(':fb' => $n));
		$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($r) === 1)
			$res[] = $r[0];
	}

	process_results($app, $res);
}


function process_result(&$app, $res) {
	if (count($res) === 1) {
		S3_init($app);
		$obj = $res[0];
		if (isset($obj['filename_profile']))
			$obj['url_profile'] = S3_resolve_filename($app, $obj['filename_profile']);
		$res = array($obj);
	}
	serve_json($app,$res,200);
}

// TODO factoriser
function process_results(&$app, $res) {
	if (count($res) > 0) {
		S3_init($app);
		foreach($res as &$obj) {
			if (isset($obj['filename_profile']))
				$obj['url_profile'] = S3_resolve_filename($app, $obj['filename_profile']);
		}
	}
	serve_json($app,$res,200);
}

