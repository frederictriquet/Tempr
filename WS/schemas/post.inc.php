<?php

require_once('tools/random.inc.php');
//require_once('tools/misc.inc.php');
require_once('tools/events.inc.php');


$app->group('/post', function () use ($app) {
	// documented
	$app->get('/:post_id','post_get')->conditions(array(
			'post_id'=>'\d{1,}'
		));
	// documented
	$app->get('/:post_id/url/','post_url_get')->conditions(array(
			'post_id'=>'\d{1,}'
		));
	// documented
	$app->post('/','post_post');
	// documented
	$app->delete('/:post_id','post_delete')->conditions(array(
			'post_id'=>'\d{1,}'
		));
	// documented
	$app->post('/phone/','post_phone_post');
	// documented
	$app->post('/fb/','post_fb_post');
});


function post_get($post_id) {
	$app = \Slim\Slim::getInstance();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;
	$stmt = $db->prepare("select * from post_get(:post_id, :user_id)");
	$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$res = retrieve_temps($app, $db, $stmt);

	serve_json($app, $res, 200);
}

function post_url_get($post_id) {
    $app = \Slim\Slim::getInstance();
    $db = db_connect();
    $pk_user_id = $app->pk_user_id;
    $stmt = $db->prepare("select * from post_can_i_share(:post_id, :user_id)");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_NUM);

    if ((count($res) === 1) && ($res[0][0])) {
        // TODO use a token
        $res = (object)['url'=> WWW_TEMPR_ME . 'p/' . $post_id];
        serve_json($app, $res, 200);
    } else {
        serve_error($app, 'Cannot share this post', 403);
    }
}



function post_post() {
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();
	$res = private_create_post($app, $body);
    serve_json($app, $res->result, $res->status);
}

function private_create_post(&$app, &$body, $pending_friendship=false, $pending_rcpt=null) {
	S3_init($app);
	$app->db = db_connect();
	$pk_user_id = $app->pk_user_id;
	$media = get_optional_value('media', $body, NULL);
	$has_media = ($media !== NULL);
	if ($pending_rcpt !== null) {
        $to_user_id = $pending_rcpt;
        $pending_friendship = true;
	} else {
	    $to_user_id = get_mandatory_value('to_user_id',$body);
	}
	if ($has_media) {
		$authorized_medias = array('image/gif','image/jpeg','image/png','video/mpeg', 'video/mp4', 'video/webm');
		if (! in_array($media, $authorized_medias)) {
			serve_error($app, 'incorrect media type');
		}
		$media_detail = explode('/', $media);
	}
	$text_body = get_optional_value('body', $body, NULL);

	$tags = get_mandatory_value('tags', $body);
	if (!is_array($tags) || (count($tags)<1) || (count($tags)>3))
		serve_error($app, 'incorrect number of tags');
	$realtags = array(NULL, NULL, NULL);
	$j = 0;
	foreach ($tags as $t) {
		if (!is_tag_valid($t))
			serve_error($app, 'incorrect tag');
		else
			$realtags[$j++] = $t;
	}

	$city_id = NULL;
	$locality = get_optional_value('locality',$body, NULL);
	if ($locality !== NULL) {
		$countryCode = get_mandatory_value('countryCode',$body);
		$stmt = $app->db->prepare('select * from devcity_create(:locality,:country)');
		$stmt->bindParam(':locality', $locality, PDO::PARAM_STR);
		$stmt->bindParam(':country', $countryCode, PDO::PARAM_STR);
		$stmt->execute();
		$res = $stmt->fetchAll(PDO::FETCH_NUM);
		$city_id = @$res[0][0];
	}

	$latitude = get_optional_value('latitude',$body, NULL);
	$longitude = get_optional_value('longitude',$body, NULL);
	
	$result = new stdClass();

	if ($has_media || $pending_friendship) {
	    if ($pending_rcpt !== null) {
            $stmt = $app->db->prepare("select * from post_create_pending_user(:body,:from_user_id,:to_user_id,:tag1,:tag2,:tag3,:city_id,:lat,:lon,:pending_reason)");
            //$stmt = $app->db->prepare("select * from post_create_pending_user(:body,:from_user_id,:to_user_id,:tag1,:tag2,:tag3,:city_id,:pending_reason)");
	    } else {
            $stmt = $app->db->prepare("select * from post_create_pending(:body,:from_user_id,:to_user_id,:tag1,:tag2,:tag3,:city_id,:lat,:lon,:pending_reason)");
            //$stmt = $app->db->prepare("select * from post_create_pending(:body,:from_user_id,:to_user_id,:tag1,:tag2,:tag3,:city_id,:pending_reason)");
	    }
        $stmt->bindParam(':body', $text_body, PDO::PARAM_STR);
        $stmt->bindParam(':from_user_id', $pk_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':to_user_id', $to_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':tag1', $realtags[0], PDO::PARAM_STR);
        $stmt->bindParam(':tag2', $realtags[1], PDO::PARAM_STR);
        $stmt->bindParam(':tag3', $realtags[2], PDO::PARAM_STR);
        $stmt->bindParam(':city_id', $city_id, PDO::PARAM_INT);
        $stmt->bindParam(':lat', $latitude, PDO::PARAM_STR);
        $stmt->bindParam(':lon', $longitude, PDO::PARAM_STR);
        
        $pending_reason = 0;
        if ($has_media) {
            if ($media_detail[0] === 'video')
                $pending_reason |= 2+4; // post video+thumbnail
            else
                $pending_reason |= 1; // post image
        }
        if ($pending_friendship)
            $pending_reason |= 16; // pending friendship
        if ($pending_rcpt !== null) {
            $pending_reason |= 8+16; // pending recipient + friendship
        }

        $stmt->bindParam(':pending_reason', $pending_reason, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_NUM);
        //elp($res);
        if ((count($res)==1) && ($res[0][0] === 'pending_post')) {
            if ($has_media) {
                $post_id = $res[0][1];
                if ($media_detail[0] === 'video') {
        			$r = pending_upload_create($app, $media_detail[0], $media_detail[1], 3, $post_id); // 3: post video
        			$result->uploadUrlVid = $r['upload_link'];
        			$result->confirmTokenVid = $r['confirm_token'];
        		    $r = pending_upload_create($app, 'image', 'jpeg', 4, $post_id); // 4: post thumbnail
        		    $result->uploadUrl = $r['upload_link'];
        		    $result->confirmToken = $r['confirm_token'];
    			} else {
        		    $r = pending_upload_create($app, $media_detail[0], $media_detail[1], 2, $post_id); // 2: post image
        		    $result->uploadUrl = $r['upload_link'];
        		    $result->confirmToken = $r['confirm_token'];
    			}
            }
        } else {
            serve_error($app, 'Post forbidden', 403);
        }
	} else {
        $stmt = $app->db->prepare("select * from posts_create(:body,:from_user_id,:to_user_id,:tag1,:tag2,:tag3,:city_id,:lat,:lon)");
        $stmt->bindParam(':body', $text_body, PDO::PARAM_STR);
        $stmt->bindParam(':from_user_id', $pk_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':to_user_id', $body['to_user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':tag1', $realtags[0], PDO::PARAM_STR);
        $stmt->bindParam(':tag2', $realtags[1], PDO::PARAM_STR);
        $stmt->bindParam(':tag3', $realtags[2], PDO::PARAM_STR);
        $stmt->bindParam(':city_id', $city_id, PDO::PARAM_INT);
        $stmt->bindParam(':lat', $latitude, PDO::PARAM_STR);
        $stmt->bindParam(':lon', $longitude, PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_NUM);
	    if ((count($res)!=1) || ($res[0][0] !== 'post')) {
	        serve_error($app, 'Post forbidden', 403);
	    }
    }

    if (count($res) == 1) {
        process_event($app, $res[0][0], $res[0][1], $res[0][2]);
    }
    
	//serve_json($app, $result, 200);
	return (object)['status'=>200, 'result'=>$result, 'id'=>$res[0][1]];
}


function is_tag_valid($t) {
	return preg_match("/.*\s.*$/", $t) !== 1;
}


function post_delete($post_id) {
	$app = \Slim\Slim::getInstance();
	$app->db = db_connect();
	$pk_user_id = $app->pk_user_id;
	
	$stmt = $app->db->prepare("select * from posts_delete(:post_id,:user_id)");
	$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
	$stmt->bindParam(':user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_NUM);
	if ((count($res)==1) && ($res[0][0] > 0)) {
        process_event($app, 'post_delete', $post_id, 0);
	    serve_nothing($app, 200);
	} else {
		serve_error($app, 'Delete post forbidden', 403);
	}
}


function post_phone_post() {
    $app = \Slim\Slim::getInstance();
    $app->db = db_connect();
    $pk_user_id = $app->pk_user_id;
    $body = $app->request->getBody();
    $phone = get_mandatory_value('phone',$body);
    $phone = str_replace(' ','',$phone);
    $phone = str_replace(' ','',$phone); // pas le même caractère !
    
    // existe-t-il dans la table users
    $stmt = $app->db->prepare('select pk_user_id from users where phone=:phone and phone_confirmed limit 1');
    $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
    $stmt->execute();
//     elp('premier select '.$stmt->rowCount());
//     elp($body);
    if ($stmt->rowCount() === 1) {
        $to_user_id = $stmt->fetchAll(PDO::FETCH_NUM)[0][0];
        $stmt = $app->db->prepare('select 1 from friendships where fk_user_id1 = :user_id1 and fk_user_id2 = :user_id2');
        $stmt->bindValue(':user_id1', $pk_user_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id2', $to_user_id, PDO::PARAM_INT);
        $stmt->execute();
        //elp('second select '.$stmt->rowCount());
        if ($stmt->rowCount() === 0) {
            // nous ne sommes pas amis
            //elp('post pending avec un vrai user + demande amitie');
            $app->db = db_connect();
            $res = request_friendship($app, $to_user_id);
            //elp($res);
            if ($res[0]['event_type'] === 'friendship_acceptance')
                $pending_friendship = false;
            else
                $pending_friendship = true;
        } else {
            $pending_friendship = false;
        }
        // construire un body avec ce pk_user_id et appeler post_post
        //elp('post normal vers un ami');
        $body['to_user_id'] = $to_user_id;
        $res = private_create_post($app, $body, $pending_friendship);
    } else {
        //elp('post vers un pending_user');
        // il n'existe pas dans users
        // on le crée (si besoin) dans pending_users
        $stmt = $app->db->prepare('select * from pending_user_create_by_phone(:phone)');
        $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
        $stmt->execute();
        $to_pending_user_id = $stmt->fetchAll(PDO::FETCH_NUM)[0][0];
        //elp('friendship_request vers ce pending_user');
        // on le demande en ami
        $stmt = $app->db->prepare('select * from pending_user_request_friendship(:user_id1,:user_id2)');
        $stmt->bindValue(':user_id1', $pk_user_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id2', $to_pending_user_id, PDO::PARAM_INT);
        $stmt->execute();
        $res = private_create_post($app, $body, true, $to_pending_user_id);
        $post_id = $res->id;
        process_event($app, 'post_by_phone', $post_id);
    }
    // TODO utiliser process_event
    //send_invitation_sms($firstname, $phone, $tag);
    serve_json($app, $res->result, $res->status);
}

// TODO FACTORISER
function post_fb_post() {
    $app = \Slim\Slim::getInstance();
    $app->db = db_connect();
    $pk_user_id = $app->pk_user_id;
    $body = $app->request->getBody();
    $fb_id = get_mandatory_value('fb',$body);
    
    // existe-t-il dans la table users
    $stmt = $app->db->prepare('select pk_user_id from users where facebook_id=:fb_id limit 1');
    $stmt->bindValue(':fb_id', $fb_id, PDO::PARAM_STR);
    $stmt->execute();
    //     elp('premier select '.$stmt->rowCount());
    //     elp($body);
    if ($stmt->rowCount() === 1) {
        $to_user_id = $stmt->fetchAll(PDO::FETCH_NUM)[0][0];
        $stmt = $app->db->prepare('select 1 from friendships where fk_user_id1 = :user_id1 and fk_user_id2 = :user_id2');
        $stmt->bindParam(':user_id1', $pk_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id2', $to_user_id, PDO::PARAM_INT);
        $stmt->execute();
        //elp('second select '.$stmt->rowCount());
        if ($stmt->rowCount() === 0) {
            // nous ne sommes pas amis
            //elp('post pending avec un vrai user + demande amitie');
            $app->db = db_connect();
            $res = request_friendship($app, $to_user_id);
            //elp($res);
            if ($res[0]['event_type'] === 'friendship_acceptance')
                $pending_friendship = false;
            else
                $pending_friendship = true;
        } else {
            $pending_friendship = false;
        }
        // construire un body avec ce pk_user_id et appeler post_post
        //elp('post normal vers un ami');
        $body['to_user_id'] = $to_user_id;
        $res = private_create_post($app, $body, $pending_friendship);
    
    } else {
        //elp('post vers un pending_user');
        // il n'existe pas dans users
        // on le crée (si besoin) dans pending_users
        $stmt = $app->db->prepare('select * from pending_user_create_by_fb(:fb_id)');
        $stmt->bindValue(':fb_id', $fb_id, PDO::PARAM_STR);
        $stmt->execute();
        $to_pending_user_id = $stmt->fetchAll(PDO::FETCH_NUM)[0][0];
        //elp('friendship_request vers ce pending_user');
        // on le demande en ami
        $stmt = $app->db->prepare('select * from pending_user_request_friendship(:user_id1,:user_id2)');
        $stmt->bindParam(':user_id1', $pk_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id2', $to_pending_user_id, PDO::PARAM_INT);
        $stmt->execute();
        $res = private_create_post($app, $body, true, $to_pending_user_id);
    }
    serve_json($app, $res->result, $res->status);
}

