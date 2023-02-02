<?php

function serve(&$app, $content_type, $body, $status) {
	$app->contentType($content_type);
	$app->response->status($status);
	$app->response->body($body);
}

function serve_json(&$app, $body_object, $status) {
	serve($app, 'application/json', json_encode($body_object), $status);
}

function serve_nothing(&$app, $status) {
	serve($app, NULL, NULL, $status);
}

function serve_error(&$app, $message, $status = 400) {
	serve_json(
			$app,
			get_error_message(
					$message,
					$app->request->getMethod(),
					$app->request->getPath()
					),
			$status);
	$app->stop();
}

