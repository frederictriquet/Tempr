<?php
require_once('tools/random.inc.php');
require_once('conf/fb.inc.php');
require_once('tools/hammering.inc.php');
require_once('tools/user.inc.php');

$app->group('/login', function () use ($app) {
	// documented
	$app->post('/', 'login_post');
	// documented
	$app->post('/fb/', 'login_fb_post');
	// documented
	$app->post('/renew/', 'login_renew_post');
});

// login/password in the json body
function login_post() {
	//error_log("Login with body information");
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$email = get_mandatory_value('email', $body);
	$password = get_mandatory_value('password', $body);

	hammering_prevent_bf_same_ip($app, $_SERVER['REMOTE_ADDR']);

	$db = db_connect();
	$stmt = $db->prepare('select pk_user_id, password from users where email=:email');
	$stmt->bindParam(':email', $email, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//error_log("Login with body information 2 ".print_r($res,1));
	if (count($res) === 1) {
		//error_log("Login with body information 3");
		$pk_user_id = $res[0]['pk_user_id'];
		$db_password = $res[0]['password'];
		//error_log($password.' --- '.$db_password);
		if (password_verify($password, $db_password)) {
        	$token = get_token();
        	$refresh_token = get_token();
			//error_log("Login with body information 4");
			//elp("token $token  refresh_token $refresh_token");
        	user_login($pk_user_id, $token, $refresh_token);
			//elp("token $token  refresh_token $refresh_token");
			/*$stmt = $db->prepare('select logged_in, pk_user_id, token, refresh_token from user_login(:pk_user_id,:token,:refresh_token)');       //
			$stmt->bindParam(':pk_user_id', $pk_user_id, PDO::PARAM_INT);                                                                        //
			$stmt->bindParam(':token', $token, PDO::PARAM_STR);                                                                                  //
			$stmt->bindParam(':refresh_token', $refresh_token, PDO::PARAM_STR);                                                                  //
			$stmt->execute();                                                                                                                    //
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);                                                                                            //
			elp("token $token  refresh_token $refresh_token");

			//error_log(print_r($res,1));
			if ($res[0]['logged_in'] === True) {
				//error_log("Login with body information 5");
				serve_json($app,$res[0], 200);
				return;
			}*/
			$res = (object)['logged_in'=>True, 'pk_user_id'=>$pk_user_id, 'token'=>$token, 'refresh_token'=>$refresh_token];
			serve_json($app,$res,200);
			return;
		}
	}
	hammering_prevent_bf_same_account($app, $email);
	serve_error($app, 'Email/Password authentication failed', 401);
}

// login / facebook token in the json body
function login_fb_post() {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();

	//$login = get_mandatory_value('login', $body);
	$token = get_mandatory_value('token', $body);

	$fb = new Facebook\Facebook([
			'app_id' => FB_APP_ID,
			'app_secret' => FB_APP_SECRET,
			'default_graph_version' => 'v2.6',
			]);
	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get('/me?fields=id,locale', $token);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		error_log('Graph returned an error: ' . $e->getMessage());
		serve_error($app, 'Graph returned an error: '.$e->getMessage(), 500);
		return;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		error_log('Facebook SDK returned an error: ' . $e->getMessage());
		$res = array('msg' => 'Facebook SDK returned an error: '.$e->getMessage());
		serve_error($app, 'Facebook SDK returned an error: '.$e->getMessage(), 500);
		return;
	}

	$user = $response->getGraphUser();
	$facebook_id = $user['id'];
    //elp('reponse');
	//elp($response);
	//elp('user');
	//elp($user);
	//error_log(print_r($body,1));
	$db = db_connect();
	$token = get_token();
	$refresh_token = get_token();
	/*
	$stmt = $db->prepare('select logged_in, pk_user_id, token, refresh_token from user_fb_login(:facebook_id,:token,:refresh_token)');
	//$stmt->bindParam(':login', $login, PDO::PARAM_STR);
	$stmt->bindParam(':facebook_id', $facebook_id, PDO::PARAM_STR);
	$stmt->bindParam(':token', $token, PDO::PARAM_STR);
	$stmt->bindParam(':refresh_token', $refresh_token, PDO::PARAM_STR);
	*/
	$stmt = $db->prepare('select pk_user_id from users where facebook_id=:facebook_id');
	$stmt->bindParam(':facebook_id', $facebook_id, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_NUM);
////
    if (count($res) === 1) {
        $pk_user_id = $res[0][0];
        user_login($pk_user_id, $token, $refresh_token);
        $res = (object)['logged_in'=>True, 'pk_user_id'=>$pk_user_id, 'token'=>$token, 'refresh_token'=>$refresh_token];
        serve_json($app,$res, 200);
//	error_log(print_r($res,1));
//	if ($res[0]['logged_in'] === True) {
    } else {
        serve_error($app, 'Facebook authentication failed', 401);
    }
}

function login_renew_post() {
	$app = \Slim\Slim::getInstance();
	$refresh_token = $app->request->headers->get('Authorization');
	$db = db_connect();
	$new_token = get_token();
	$renewed = user_renew_token($refresh_token, $new_token);
    elp('renewed1 '.($renewed?'true':'false'));
	/*$stmt = $db->prepare('select user_renew_token(:refresh_token,:new_token)');
	$stmt->bindParam(':refresh_token', $refresh_token, PDO::PARAM_STR);
	$stmt->bindParam(':new_token', $new_token, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if ((count($res) == 1) && (array_key_exists('user_renew_token',$res[0]))) {
		$renewed = (bool)$res[0]['user_renew_token'];
		*/
    {
		if ($renewed) {
            elp('renewed2 '.($renewed?'true':'false'));
		    serve_json($app, (object)array('token'=>$new_token) , 200);
			return;
		}
	}
	serve_error($app, 'dead refresh token', 401);
}