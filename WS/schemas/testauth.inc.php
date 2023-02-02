<?php

$app->group('/testauth', function () use ($app) {
	// on purpose not documented
	$app->get('/', 'get_testauth');
});


function get_testauth() {
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	$app->response->body('{"response":"It\'s alive!","pk_user_id":'.$app->pk_user_id.'}');
}
