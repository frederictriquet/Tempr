<?php
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

// Set the current mode
$app = new \Slim\Slim(array(
		'mode' => ENVIRONMENT
));


// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
	$app->config(array(
			'log.enable' => true,
			'debug' => false
	));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
	$app->config(array(
			'log.enable' => true,
			'debug' => true
	));

});




/*
$app->error(function (\Exception $e) use ($app) {
	error_log('YESSS');
	//error_log(print_r($e,1));
	//serve_error($app, print_r($e,1), 500);
});*/




$app->notFound(function () use ($app) {
	serve_error($app,'Page not found', 404);
});
