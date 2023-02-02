<?php
require 'vendor/autoload.php';
require 'conf/env.inc.php';
require 'tools/db.inc.php';
require 'slim.inc.php';
require 'tools/middleware.inc.php';
require 'tools/misc.inc.php';
require 'tools/serve.inc.php';
// here $app is a Slim instance

// require all the files related to the available schemas
// each file will register its callbacks
$schemas = array(
		'test','testauth',
		'posts','post','friend','flow','media',
		'like','unlike','comment','comments',
		'tags','hello',
		'profile','pending',
		'noauth',
		'login','logout',
		'friends','friendship',
		'search','events','event','report',
		'cities','es'
		);
/*foreach ($schemas as $schema) {
	require 'schemas/'.$schema.'.inc.php';
}*/

/////////////////////////////////////////////////////////////////////////////////
//$app->add(new \HttpBasicAuth());
$app->add(new \TokenAuth());
$app->add(new \Slim\Middleware\ContentTypes());

// routes lazy loading depending on the schema
$app->hook("slim.before.router",function() use ($app, $schemas){
	//error_log($app->request()->getPathInfo());
	/*foreach ($schemas as $schema) {
		if (strpos($app->request()->getPathInfo(), "/".$schema."/") === 0) {
			require_once('schemas/'.$schema.'.inc.php');
			return;
		}
	}*/
	$a = explode('/',$app->request()->getPathInfo());
	$schema = $a[1];
	if (in_array($schema, $schemas)) {
		require_once 'schemas/'.$schema.'.inc.php';
	}
});

$app->run();
/////////////////////////////////////////////////////////////////////////////////
