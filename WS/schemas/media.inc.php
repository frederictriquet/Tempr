<?php

// UNUSED ???

use Aws\S3\S3Client;
require_once('tools/random.inc.php');

$app->group('/media', function () use ($app) {
	// not documented UNUSED
	$app->post('/','media_post');
});


function media_post() {
	$app = \Slim\Slim::getInstance();
	$pk_user_id = $app->request->headers('PHP_AUTH_USER');

	$s3 = new S3Client(['region'=>'us-west-2', 'version'=>'latest']);
	$bucket = 'tempr.co.dev.oregon';
	$dest_filename = 'a/b.txt';
	$upload='';

	$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	$dest_filename = $pk_user_id.'/'.create_uploaded_filename().'.'.$ext;

	$db = db_connect();
	$stmt = $db->prepare("select * from media_create(:filename)");
	$stmt->bindParam(':filename', $dest_filename, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$res = (object)['media'=>$res[0]['media_create']];
	$upload = $s3->upload($bucket, $dest_filename, fopen($_FILES['file']['tmp_name'], 'rb'), 'authenticated-read'); // authenticated-read | private
	serve_json($app, $res, 200);
}
