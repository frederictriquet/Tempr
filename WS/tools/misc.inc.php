<?php
use Aws\S3\S3Client;
require_once('conf/aws.inc.php');
$app->S3 = $S3;

use Mailgun\Mailgun;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once('tools/random.inc.php');

function elp($a) {
	error_log(print_r($a,1));
}
function el($a) {
	error_log($a);
}

function get_mandatory_value($key, &$array) {
	if (is_array($array) && array_key_exists($key, $array)) {
		return $array[$key];
	}
	$app = \Slim\Slim::getInstance();
	serve_error($app, 'Missing body value: '.$key, 400);
}

function get_optional_value($key, &$array, $default_value) {
	if (is_array($array) && array_key_exists($key, $array)) {
		return $array[$key];
	}
	return $default_value;
}

function get_error_message($message, $method, $path) {
	return (object) array(
			'message' => $message,
			'method' => $method,
			'path' => $path
			);
}


function S3_init(&$app) {
	$app->s3client = S3Client::factory($app->S3['conf']);
}
function S3_resolve_filename(&$app, $filename) {
	$res = null;
	if (!is_null($filename)) {
		$cmd = $app->s3client->getCommand('GetObject', [
				'Bucket' => $app->S3['bucket'],
				'Key'    => $filename
				]);
		$request = $app->s3client->createPresignedRequest($cmd, '+60 minutes');
		$res = (string) $request->getUri();
	}
	return $res;
}

// base_type = image | video
// type = gif|jpeg|png | mpeg|mp4|webm
// TODO USING $type AS FILE EXTENSION MAY NOT BE GOOD (?)
function S3_create_put_link(&$app, $folder, $base_type, $type) {
	$dest_filename = $folder.'/'.create_uploaded_filename(). '.'.$type;
	$command = $app->s3client->getCommand('PutObject', array(
			'Bucket'      => $app->S3['bucket'],
			'Key'         => $dest_filename,
			'ContentType' => $base_type.'/'.$type,
			'Body'        => ''
	));
	$request = $app->s3client->createPresignedRequest($command,'+50 minutes');
	$signed_url = (string) $request->getUri();
	return array('dest_filename' => $dest_filename, 'signed_url' => $signed_url);
}



/*
 * $base_type = image|video
 * $type = gif|jpeg|png | mpeg|mp4|webm
 * $destination = 0 (profile), 1 (background), 2 (post image), 3 (post video)
 */
function pending_upload_create(&$app, $base_type, $type, $destination, $post_id = NULL) {
	$pk_user_id = $app->pk_user_id;
	$upload_link = S3_create_put_link($app, $pk_user_id, $base_type, $type);

	$confirm_token = get_token();

	$stmt = $app->db->prepare('select * from pending_upload_create(:token, :user_id, :filename, :destination, :post_id)');
	$stmt->bindParam(':token', $confirm_token, PDO::PARAM_STR);
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':filename', $upload_link['dest_filename'], PDO::PARAM_STR);
	$stmt->bindParam(':destination', $destination, PDO::PARAM_INT);
	$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
	$stmt->execute();
	return array('upload_link'=>$upload_link['signed_url'], 'confirm_token'=>$confirm_token);
}


function make_login($firstname, $lastname) {
	$firstname = trans($firstname);
	$lastname = trans($lastname);
	$login = substr($firstname.'-'.$lastname, 0, 240);
	return $login;
}

function trans($s) {
	return preg_replace('/[^a-zA-Z0-9\-]/','_',
			transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove',$s)
	);
	//return transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower()',$s);
}

function password_is_safe($password) {
	return strlen($password) > 7;
	//return (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password));
}

/*
function send_mail($to, $subject, $html) {
	// FIXME DEPLOY
	$mg = new Mailgun('key-99125d6b68c3a6cbe2355172f5ffd002');
	$domain = "tempr.me";
	$result = $mg->sendMessage($domain, array(
			'from' => 'Tempr No-Reply <noreply@tempr.me>',
			'to' => $to,
			'subject' => $subject,
			'html' => $html
			));
}

function send_mail_reset_pass($email, $token) {
	$url = WWW_TEMPR_ME.'reset/'.$token;
	send_mail($email,
			'[Tempr] Did you lose your password ?',
			'<html>Click here: <a href="' . $url .'">'.$url.'</a></html>'
			);
}

function send_mail_confirm($email, $token) {
	$url = WWW_TEMPR_ME.'confirm/'.$token;
	send_mail($email,
			'[Tempr] Please confirm your e-mail address',
			'<html>Click here: <a href="' . $url .'">'.$url.'</a></html>'
			);
}
*/

function send_to_rabbit($queue, $obj) {
	$connection = new AMQPStreamConnection(TEMPR_MQ_HOST, 5672, 'lapin', 'lapin');
	$channel = $connection->channel();
	$channel->queue_declare($queue, false, false, false, false);

	$msg = new AMQPMessage(json_encode($obj));
	$channel->basic_publish($msg, '', $queue);
	$channel->close();
	$connection->close();
}


function send_mail_reset_pass($email, $token) {
	$obj = (object)array(
			'action'=>'sendmail',
			'subaction'=>'resetpass',
			'to'=>$email,
			'token'=>$token
		);
	send_to_rabbit('mail',$obj);
}

function send_mail_confirm($email, $token) {
	$obj = (object)array(
			'action'=>'sendmail',
			'subaction'=>'confirm',
			'to'=>$email,
			'token'=>$token
		);
	send_to_rabbit('mail', $obj);
}

function send_mail_new_user($name, $nb) {
	$obj = (object)array(
			'action'=>'sendmail',
			'subaction'=>'new_user',
	        'name'=>$name,
	        'nb'=>$nb
   		);
	send_to_rabbit('mail', $obj);
}

function send_confirmation_sms($pk_user_id,$to,$code) {
	$obj = (object)array(
	        'action'=>'sendsms',
	        'user_id'=>$pk_user_id,
	        'to'=>$to,
	        'code'=>$code
	);
	send_to_rabbit('sms', $obj);
}

function send_invitation_sms($firstname, $to, $tag) {
	$obj = (object)array(
			'action'=>'sendsms',
	        'firstname'=>$firstname,
			'to'=>$to,
			'tag'=>$tag
	);
	send_to_rabbit('sms', $obj);
}

/* unused ?
function send_push_notification($to,$code) {
	$obj = (object)array(
			'action'=>'sendpush',
			'to'=>$to,
			'code'=>$code
	);
	send_to_rabbit('push', $obj);
}
*/

function retrieve_city_id(&$db, $lat, $lon) {
	$lat = (double)$lat;
	$lon = (double)$lon;

	$stmt = $db->prepare('select * from city_get(:lat, :lon)');
	$stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
	$stmt->bindParam(':lon', $lon, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($res) == 1) {
		return $res[0]['pk_city_id'];
	}
	return NULL;
}


function retrieve_do_i_like($db, $user_id, $post_ids) {
	$stmt = $db->prepare('select * from flow_get_do_i_like(:user_id, :post_ids)');
	$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
	$p = '{'.implode(',',$post_ids).'}';
	$stmt->bindParam(':post_ids', $p);
	$stmt->execute();
	$likes = $stmt->fetchAll(PDO::FETCH_ASSOC);

	//elp($res2);
	//error_log(print_r($res,1));
	$res_likes = array();
	foreach ($likes as $l) {
		@$res_likes[$l['pk_post_id']][] = $l['ck_seq_id'];
	}
	if (empty($res_likes)) $res_likes = new stdClass();
	return $res_likes;
}


// $stmt must contain a statement returning a list of view_decorated_posts
// such as flow_posts_get() or post_get()
function retrieve_temps(&$app, &$db, &$stmt) {
	S3_init($app);
	$stmt->execute();
	$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$post_ids = array();
	foreach ($posts as &$obj) {
	    unset($obj['fk_media_id']);
	    $obj['url'] = S3_resolve_filename($app, $obj['filename']);
		unset($obj['filename']);
		$obj['url_vid'] = S3_resolve_filename($app, $obj['filename_vid']);
		unset($obj['filename_vid']);
		$post_ids[] = $obj['pk_post_id'];
		$obj['from_url_profile'] = S3_resolve_filename($app, $obj['from_filename_profile']);
		unset($obj['from_filename_profile']);
		$obj['to_url_profile'] = S3_resolve_filename($app, $obj['to_filename_profile']);
		unset($obj['to_filename_profile']);
	}

	$res_likes = retrieve_do_i_like($db, $app->pk_user_id, $post_ids);

	return array('posts'=>$posts, 'likes'=>$res_likes);
}

function request_friendship(&$app, $other_user_id) {
    $id = $app->pk_user_id;
    $stmt = $app->db->prepare('select * from friendship_request_create(:user_id1,:user_id2)');
    $stmt->bindParam(':user_id1', $id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id2', $other_user_id, PDO::PARAM_INT);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($res) == 1) {
        process_event($app, $res[0]['event_type'], $res[0]['event_data'], $res[0]['event_data2']);
    }
    return $res;
}


