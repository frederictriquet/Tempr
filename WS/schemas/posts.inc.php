<?php

require_once('tools/random.inc.php');
//require_once('tools/misc.inc.php');


$app->group('/posts', function () use ($app) {
	// documented
	$app->get('/:user_id/:tag_id(/down/:ts)','posts_with_tag_down_get')->conditions(array(
			'user_id'=>'\d{1,}',
			'tag_id'=>'\d{1,}'
		));
	// documented
	$app->get('/:user_id/:tag_id/up/:ts','posts_with_tag_up_get')->conditions(array(
			'user_id'=>'\d{1,}',
			'tag_id'=>'\d{1,}'
		));
	// documented
	$app->get('/:user_id/media/(down/:ts)','posts_with_media_down_get')->conditions(array(
			'user_id'=>'\d{1,}'
		));
	// documented
	$app->get('/:user_id/media/up/:ts','posts_with_media_up_get')->conditions(array(
			'user_id'=>'\d{1,}'
		));
});



function posts_with_tag_down_get($user_id, $tag_id, $ts = null) {
	$app = \Slim\Slim::getInstance();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;
	//error_log("checking $user_id's posts");
	if ($ts !== null) {
		$stmt = $db->prepare("select * from posts_by_tag_down_get(:viewer_user_id, :user_id, :tag_id, :before)");
		$stmt->bindParam(':before', $ts, PDO::PARAM_STR);
	} else {
		$stmt = $db->prepare("select * from posts_by_tag_down_get(:viewer_user_id, :user_id, :tag_id)");
	}
	$stmt->bindParam(':viewer_user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
	$stmt->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
	$res = retrieve_temps($app, $db, $stmt);

	serve_json($app, $res, 200);
}

function posts_with_tag_up_get($user_id, $tag_id, $ts) {
	$app = \Slim\Slim::getInstance();
	$db = db_connect();
	$pk_user_id = $app->pk_user_id;
	//error_log("checking $user_id's posts");
	$stmt = $db->prepare("select * from posts_by_tag_up_get(:viewer_user_id, :user_id, :tag_id, :after)");
	$stmt->bindParam(':viewer_user_id', $pk_user_id, PDO::PARAM_INT);
	$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
	$stmt->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
	$stmt->bindParam(':after', $ts, PDO::PARAM_STR);
	$res = retrieve_temps($app, $db, $stmt);

	serve_json($app, $res, 200);
}




function posts_with_media_down_get($user_id, $ts = null) {
    $app = \Slim\Slim::getInstance();
	S3_init($app);
    $db = db_connect();
    $pk_user_id = $app->pk_user_id;
    //error_log("checking $user_id's posts");
    if ($ts !== null) {
        $stmt = $db->prepare("select * from posts_with_media_down_get(:viewer_user_id, :user_id, :before)");
        $stmt->bindParam(':before', $ts, PDO::PARAM_STR);
    } else {
        $stmt = $db->prepare("select * from posts_with_media_down_get(:viewer_user_id, :user_id)");
    }
    $stmt->bindParam(':viewer_user_id', $pk_user_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $res = [];
	foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
	    $res[] = (object)[
	        'url' => S3_resolve_filename($app, $r['filename']),
	        'post_id' => $r['fk_post_id'],
	        'creation_ts' => $r['creation_ts']
        ];
	}
    //elp($res);
    serve_json($app, $res, 200);
}


function posts_with_media_up_get($user_id, $ts) {
    $app = \Slim\Slim::getInstance();
	S3_init($app);
    $db = db_connect();
    $pk_user_id = $app->pk_user_id;
    //error_log("checking $user_id's posts");
    $stmt = $db->prepare("select * from posts_with_media_up_get(:viewer_user_id, :user_id, :after)");
    $stmt->bindParam(':viewer_user_id', $pk_user_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':after', $ts, PDO::PARAM_STR);
    $stmt->execute();
    
    $res = [];
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $res[] = (object)[
        'url' => S3_resolve_filename($app, $r['filename']),
        'post_id' => $r['fk_post_id'],
        'creation_ts' => $r['creation_ts']
        ];
    }

    serve_json($app, $res, 200);
}
