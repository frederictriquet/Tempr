<?php

require_once('tools/events.inc.php');
require_once('conf/fb.inc.php');

$app->group('/profile', function () use ($app) {
	// documented
	$app->get('/(:userid)', 'profile_get')->conditions(array('userid'=>'\d{1,}'));
	// documented
	$app->get('/url/', 'profile_url_get');
	// documented
	$app->get('/:userid/recenttags/', 'profile_recenttags_get')->conditions(array('userid'=>'\d{1,}'));
	// documented
	$app->get('/:userid/alltags/', 'profile_alltags_get')->conditions(array('userid'=>'\d{1,}'));
	// documented
	$app->post('/infos/', 'profile_infos_post');
	// documented
	$app->get('/confirm/email/', 'profile_confirm_email_get');
	// documented
	$app->get('/confirm/phone/', 'profile_confirm_phone_get');
	// documented
	$app->post('/confirm/phone/:code', 'profile_confirm_phone_post')->conditions(array('code'=>'\d{4}'));
	// documented
	$app->post('/fb/:fbtoken', 'profile_fb_post');
	// documented
	$app->delete('/fb/', 'profile_fb_delete');
	// documented
	$app->post('/:category/:type', 'profile_newpic_post');
	// documented
	$app->delete('/', 'profile_delete');
});


function profile_get($user_id = 0) {
	$app = \Slim\Slim::getInstance();
	S3_init($app);
	$body = $app->request->getBody();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;
	if ($user_id === 0) {
		$other_user_id = $pk_user_id;
		$stmt = $db->prepare("select * from profile_get(:user_id)");
	} else {
		$other_user_id = $user_id;
		$stmt = $db->prepare("select * from profile_get(:user_id, :other_user_id)");
		$stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
	}
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($res) == 0) {
		serve_error($app, 'No such user', 404);
	} else {
		$obj = $res[0];
		if (isset($obj['filename_profile']))
			$obj['url_profile'] = S3_resolve_filename($app, $obj['filename_profile']);

		// SELF PROFILE (must be complete, but without additional data)
		if ($user_id === 0) {
			if (isset($obj['filename_background']))
				$obj['url_background'] = S3_resolve_filename($app, $obj['filename_background']);
			// retrieve 'has_events'
			$obj['has_events'] = events_exist($db, $pk_user_id);
		}
		// FULL PROFILE
		else if (array_key_exists('is_full', $obj) && ($obj['is_full'] === True)) {
			if (isset($obj['filename_background']))
				$obj['url_background'] = S3_resolve_filename($app, $obj['filename_background']);

			// retrieve number of friends
			$stmt = $db->prepare("select count(*) from friendships where fk_user_id1 = :other_user_id");
			$stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
			$stmt->execute();
			$obj['nbfriends'] = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['count'];

			// retrieve friends
			// user_id, firstname, lastname, photo
			$stmt = $db->prepare("select * from profile_friends_of(:other_user_id,0) ORDER BY firstname, lastname");
			$stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
			$stmt->execute();
			$obj['friends'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($obj['friends'] as &$f) {
				$f['url_profile'] = S3_resolve_filename($app, $f['filename_profile']);
			}

			// top5 hashtags 15 derniers jours
			$stmt = $db->prepare("select fk_htag_id, tag, pop from view_user_recent_trends WHERE fk_user_id = :other_user_id order by pop desc, tag asc limit 5");
			$stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
			$stmt->execute();
			$obj['recent_trends'] = array('id'=>[],'tag'=>[],'likes'=>[]);
			//$obj['recent_trends'] = array('tag'=>[],'likes'=>[]);
			foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
				$obj['recent_trends']['id'][] = $r['fk_htag_id'];
				$obj['recent_trends']['tag'][] = $r['tag'];
				$obj['recent_trends']['likes'][] = $r['pop'];
			}

			// top5 hashtags ever
			$stmt = $db->prepare("select fk_htag_id, tag, pop from view_user_long_term_trends WHERE fk_user_id = :other_user_id order by pop desc, tag asc limit 5");
			$stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
			$stmt->execute();
			$obj['longterm_trends'] = array('id'=>[],'tag'=>[],'likes'=>[]);
			//$obj['longterm_trends'] = array('tag'=>[],'likes'=>[]);
			foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
				$obj['longterm_trends']['id'][] = $r['fk_htag_id'];
				$obj['longterm_trends']['tag'][] = $r['tag'];
				$obj['longterm_trends']['likes'][] = $r['pop'];
			}

			// medias récents
 			$stmt = $db->prepare("select fk_post_id, filename from profile_get_recent_medias(:other_user_id)");
			$stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
			$stmt->execute();
			$obj['recent_medias'] = array('url'=>[], 'post_id'=>[]);
			foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
				$obj['recent_medias']['url'][] = S3_resolve_filename($app, $r['filename']);
				$obj['recent_medias']['post_id'][] = $r['fk_post_id'];
			}
		}
		unset($obj['password']); // because you never know
		//error_log(print_r($obj,1));
		serve_json($app, $obj, 200);
	}
}

function profile_url_get() {
    $app = \Slim\Slim::getInstance();
    $body = $app->request->getBody();
    $db = db_connect();
    $pk_user_id = $app->pk_user_id;   
    $stmt = $db->prepare('select login from users where pk_user_id=:user_id');
    $stmt->bindValue(':user_id', $pk_user_id, PDO::PARAM_INT);
    $stmt->execute();
    $logins = $stmt->fetchAll(PDO::FETCH_NUM);
    $res = new stdClass();
    //elp($logins);
    el(count($logins));
    if (count($logins) === 1) {
        $login = $logins[0][0];
        $res->url = WWW_TEMPR_ME . 'u/' . $login;
        serve_json($app, $res, 200);
    } else
        serve_error($app, 'Cannot build profile url', 403);
}


// TODO factoriser
// TODO voir si necessaire de restreindre aux amis ou profils publics
function profile_recenttags_get($user_id) {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;

	// top des hashtags 15 derniers jours
	$stmt = $db->prepare("select fk_htag_id, tag, pop from view_user_recent_trends WHERE fk_user_id = :other_user_id order by pop desc, tag asc limit 10");
	$stmt->bindParam(':other_user_id', $user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = array('tag'=>[],'id'=>[],'likes'=>[]);
	foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
		$res['tag'][] = $r['tag'];
		$res['id'][] = $r['fk_htag_id'];
		$res['likes'][] = $r['pop'];
	}
	serve_json($app, $res, 200);
}


function profile_alltags_get($user_id) {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;

	// top40 hashtags ever
	$stmt = $db->prepare("select fk_htag_id, tag, pop from view_user_long_term_trends WHERE fk_user_id = :other_user_id order by pop desc, tag asc limit 40");
	$stmt->bindParam(':other_user_id', $user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = array('tag'=>[],'id'=>[],'likes'=>[]);
	foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
		$res['tag'][] = $r['tag'];
		$res['id'][] = $r['fk_htag_id'];
		$res['likes'][] = $r['pop'];
	}
	serve_json($app, $res, 200);
}


function profile_infos_post() {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$db = db_connect();

	$pk_user_id = $app->pk_user_id;

	$field = get_mandatory_value('field',$body);
	$authorized_fields = array('private','firstname','lastname','birthdate','city','email','phone','language','iosdevice'
			,'pn_postaboutyou','pn_friendshiprequest','pn_frienshipacceptance','pn_profileupdated'
	        ,'pn_comment','pn_like');
	if (! in_array($field, $authorized_fields)) {
		serve_error($app, 'Unauthorized field name '.$field);
	}
	$value = get_mandatory_value('value',$body);

	// TODO CLEAN THIS
	$pdo_type = PDO::PARAM_STR;
	switch ($field) {
		case 'private':
			$stmt = $db->prepare('update users set private=:value where pk_user_id=:user_id');
			$pdo_type = PDO::PARAM_BOOL;
			break;
		case 'firstname':
			$stmt = $db->prepare('update users set firstname=:value where pk_user_id=:user_id');
			$value = substr($value, 0, 120);
			break;
		case 'lastname':
			$stmt = $db->prepare('update users set lastname=:value where pk_user_id=:user_id');
			$value = substr($value, 0, 120);
			break;
		case 'birthdate':
			$stmt = $db->prepare('update users set birthdate=:value where pk_user_id=:user_id');
			break;
		case 'city':
			$stmt = $db->prepare('update users set city=:value where pk_user_id=:user_id');
			$value = substr($value, 0, 50);
			break;
		case 'email':
			$stmt = $db->prepare('update users set email=:value, email_confirmed=FALSE where pk_user_id=:user_id');
			$value = substr($value, 0, 128);
			break;
		case 'phone':
			$stmt = $db->prepare('update users set phone=:value, phone_confirmed=FALSE where pk_user_id=:user_id');
			$value = substr($value, 0, 50);
			break;
		case 'language':
			$stmt = $db->prepare('update users set lang=:value where pk_user_id=:user_id');
			$value = substr($value, 0, 2);
			break;
		case 'iosdevice':
			$stmt = $db->prepare('select user_add_iosdevice(:user_id,:value)');
			$value = substr($value, 0, 255);
			break;
		case 'pn_postaboutyou':
		case 'pn_friendshiprequest':
		case 'pn_frienshipacceptance':
		case 'pn_profileupdated':
		case 'pn_comment':
		case 'pn_like':
			$r = 'update users set ' . $field .'=:value where pk_user_id=:user_id';
			$stmt = $db->prepare($r);
			$pdo_type = PDO::PARAM_BOOL;
			break;
		default:
			serve_error($app, 'should not happen', 500);
	}
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':value', $value, $pdo_type);
	$stmt->execute();

	//serve_json($app,NULL,200);
	serve_nothing($app,200);
}

function profile_confirm_email_get() {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->pk_user_id;
	$db = db_connect();
	$stmt = $db->prepare('select email from users where pk_user_id=:user_id');
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$email = $res[0]['email'];
	if (!empty($email)) {
		$token = get_token();
		$stmt = $db->prepare('insert into email_confirmations(fk_user_id,token) values(:user_id,:token)');
		$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
		$stmt->bindParam(':token', $token, PDO::PARAM_STR);
		$stmt->execute();
		send_mail_confirm($email, $token);
		serve_nothing($app, 200);
	} else {
		serve_error($app, 'User has no e-mail address', 409);
	}
}



function profile_confirm_phone_get() {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->pk_user_id;
	$db = db_connect();
	$stmt = $db->prepare('select phone from users where pk_user_id=:user_id');
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$phone = $res[0]['phone'];
	if (!empty($phone)) {
		$code = get_pincode();
		$stmt = $db->prepare('insert into phone_confirmations(fk_user_id,code) values(:user_id,:code)');
		$stmt->bindValue(':user_id', $pk_user_id, PDO::PARAM_INT);
		$stmt->bindValue(':code', $code, PDO::PARAM_INT);
		$stmt->execute();
		send_confirmation_sms($pk_user_id,$phone,$code);
		serve_nothing($app, 200);
	} else {
		serve_error($app, 'User has no phone number', 409);
	}
}


function profile_confirm_phone_post($code) {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->pk_user_id;
	$db = db_connect();
	$stmt = $db->prepare('select user_confirm_phone(:user_id,:code)');
	$stmt->bindValue(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindValue(':code', $code, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if ((count($res) === 1)
		&& (array_key_exists('user_confirm_phone', $res[0]))
		&& ($res[0]['user_confirm_phone'] === True)
		)
	{
	    process_event($app, 'phone_confirmed', $pk_user_id);
		serve_nothing($app, 200);
	} else
		serve_error($app, 'Confirmation failed', 403);
}




function profile_newpic_post($category, $type) {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();

	$pk_user_id = $app->pk_user_id;

	$authorized_categories = array('profile','background');
	$destination = array_search($category, $authorized_categories);
	// MUST BEHAVE LIKE IN THE DATABASE
	// destination = 0  => profile
	// destination = 1  => background
	if ($destination === FALSE) {
		serve_error($app, 'Only profile or background please');
	}

	$authorized_image_types = array('gif','jpeg','png');
	if (! in_array($type, $authorized_image_types)) {
		serve_error($app, 'Only gif, jpeg and png please');
	}

	$app->db = db_connect();

	S3_init($app);
	$res = pending_upload_create($app, 'image', $type, $destination);

	$result = (object)array(
			'uploadUrl' => $res['upload_link'],
			'confirmToken' => $res['confirm_token']
	);

	serve_json($app, $result, 200);
}

function profile_fb_post($fb_token) {
    $app = \Slim\Slim::getInstance();
    $body = $app->request->getBody();
    
    $fb = new Facebook\Facebook([
            'app_id' => FB_APP_ID,
            'app_secret' => FB_APP_SECRET,
            'default_graph_version' => 'v2.6',
            ]);
    try {
        $response = $fb->get('/me?fields=id', $fb_token);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        el('Graph returned an error: ' . $e->getMessage());
        serve_error($app, 'Graph returned an error: '.$e->getMessage(), 500);
        return;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        el('Facebook SDK returned an error: ' . $e->getMessage());
        $res = array('msg' => 'Facebook SDK returned an error: '.$e->getMessage());
        serve_error($app, 'Facebook SDK returned an error: '.$e->getMessage(), 500);
        return;
    }

    $user = $response->getGraphUser();
    $fb_id = $user['id'];
    $db = db_connect();
    $stmt = $db->prepare('update users set facebook_id = :fb_id where pk_user_id = :pk_user_id');
    $stmt->bindValue(':fb_id', $fb_id, PDO::PARAM_STR);
    $stmt->bindValue(':pk_user_id', $app->pk_user_id, PDO::PARAM_INT);
    $stmt->execute();
    process_event($app, 'fb_connected', $app->pk_user_id);
    serve_nothing($app, 200);
}

function profile_fb_delete() {
    $app = \Slim\Slim::getInstance();
    $body = $app->request->getBody();
    $db = db_connect();
    $sql = 'UPDATE users SET facebook_id = NULL WHERE pk_user_id = :pk_user_id AND password IS NOT NULL';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':pk_user_id', $app->pk_user_id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() === 1) {
        serve_nothing($app, 200);
    } else {
        serve_error($app, 'Can not unlink Facebook account', 403);
    }
}


function profile_delete() {
    $app = \Slim\Slim::getInstance();
//     $db = db_connect();
//     $stmt = $db->prepare('delete from users where pk_user_id = :user_id');
//     $stmt->bindValue(':user_id', $app->pk_user_id, PDO::PARAM_INT);
//     $stmt->execute();
    process_event($app, 'user_deleted', (int)$app->pk_user_id);
    serve_nothing($app, 204);
}

