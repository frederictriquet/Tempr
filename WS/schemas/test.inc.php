<?php
use Aws\S3\S3Client;

define('AWS_ACCESS_KEY_ID','AKIAIWQ6HFIWZVUC7JLQ'); // FIXME DEPLOY
define('AWS_SECRET_ACCESS_KEY','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');

require_once('tools/events.inc.php');

$app->group('/test', function () use ($app) {
	$app->get('/', 'get_test');
	$app->get('/error/', 'get_test_error');
	$app->get('/dump1/', 'get_dump');
	$app->get('/dump2/', 'get_dump');

	$app->get('/htags/:prefix', 'get_test_htags');
	$app->get('/names/:prefix', 'get_test_names');
	$app->get('/s3/', 'get_test_s3');
	$app->post('/', 'post_test');
	$app->get('/fb/', 'get_fb');

	$app->post('/randomizelikes/(:duration)', 'post_randomizelikes');

	$app->post('/profile/confirm/phone/:code', 'test_profile_confirm_phone_post');
});


function get_test() {
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body('{"response":"It\'s alive!"}');
}

function get_test_error() {
	$app = \Slim\Slim::getInstance();
	$a = array();
	$x = get_mandatory_value('a', $a);
	// we should not see that
	serve_error($app, 'this is an error', 402);
}

function get_dump() {
	$app = \Slim\Slim::getInstance();
	//$app->response->headers->set('Content-Type', 'application/json');
	$app->response->headers->set('Content-Type', 'text/html');
	$s = 'GET <pre>'.print_r($_GET,1).'</pre>';
	$s .= 'COOKIE <pre>'.print_r($_COOKIE,1).'</pre>';
	$s .= 'REQUEST <pre>'.print_r($_REQUEST,1).'</pre>';
	$s .= 'REQUEST <pre>'.print_r($_SERVER,1).'</pre>';
	$s .= '<form action="POST"><input name="token" type=""text" /><input type="submit"/></form>';
	$app->response->body($s);
}

function get_test_htags($prefix) {
	$client = new Elasticsearch\Client();
	$searchParams['index'] = 'htags';
	$searchParams['type']  = 'htags';
	$searchParams['body']['query']['bool']['must']['prefix']['htags.tag'] = $prefix;
	$retDoc = $client->search($searchParams);

	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	echo 'callback(' . json_encode($retDoc) .')';
}

function get_test_names($prefix) {
	$client = new Elasticsearch\Client();
	$searchParams['index'] = 'users';
	$searchParams['type']  = 'names';
	$searchParams['body']['query']['bool']['must']['prefix']['names.firstname'] = $prefix;
	$retDoc = $client->search($searchParams);

	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	echo 'callback(' . json_encode($retDoc) .')';
}

function get_test_s3() {
	$s3 = new S3Client([
			'region' => 'us-west-2',
			'version' => 'latest'
		]);
	$bucket = 'tempr.co.dev.oregon';
	$cmd = $s3->getCommand('GetObject', [
			'Bucket' => $bucket,
			'Key'    => 'carreau.png'
			]);

	$request = $s3->createPresignedRequest($cmd, '+2 minutes');

	// Get the actual presigned-url
	$presignedUrl = (string) $request->getUri();
	$res = new stdClass();
	$res->url = $presignedUrl;
	echo json_encode($res);
}


function post_test() {
	$app = \Slim\Slim::getInstance();
	error_log(print_r($app->request->getBody(),1));
	error_log(print_r($_FILES,1));
	serve($app, 'text/html', '', 200);
}

function get_fb() {
	$app = \Slim\Slim::getInstance();
	$tok = 'CAAHwP2sB3ZAUBAKKuB2GUuWN8727zTsGUdZBU7vKu944YxuBHIV98b3mps2vZAKawkikepuZCGHyIycprHE7JJXdcogZCiml5QZBymooa6WC7hCSooLct1ZBYz3buLyzOsjCo1AZCTpTzjKmg8zyEJdrU0U2lRaMdfevqP2IbCNOiq7SOdt1goktGG7IQbZBZAluA0G7bWJkRXagZDZD';
	//$accessToken = new Facebook\Entities\AccessToken($tok);
	$fb = new Facebook\Facebook([
			'app_id' => FB_APP_ID,
			'app_secret' => FB_APP_SECRET,
			'default_graph_version' => 'v2.6',
			]);

	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get('/me?fields=id,last_name,first_name', $tok);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	$user = $response->getGraphUser();
	//$res = print_r($accessToken,1).print_r($fb,1);
	$res = print_r($user,1);
	var_dump($user);
	serve($app, 'text/html', $res, 200);
}

function post_randomizelikes($duration = '52weeks') {
	$app = \Slim\Slim::getInstance();
	$db = db_connect();
	$stmt = $db->prepare("select * from dev_randomizelikes(:duration)");
	$stmt->bindParam(':duration', $duration, PDO::PARAM_STR);
	$stmt->execute();
	serve_nothing($app, 200);
}



function test_profile_confirm_phone_post($code) {
    $app = \Slim\Slim::getInstance();
    $pk_user_id = (int)$code;
    $db = db_connect();
    elp('PHONE CONFIRM '.$pk_user_id. ' '.gettype($pk_user_id));
    $stmt = $db->prepare('UPDATE users SET phone_confirmed=TRUE WHERE pk_user_id = :user_id');
    $stmt->bindValue(':user_id', $pk_user_id, PDO::PARAM_INT);
    $stmt->execute();
    elp('PHONE CONFIRM '.$pk_user_id. ' '.gettype($pk_user_id));
    process_event($app, 'phone_confirmed', (int)$pk_user_id);
    
    serve_nothing($app, 200);
}


