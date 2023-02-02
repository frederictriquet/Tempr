<?php
require_once('conf/es.inc.php');

$app->group('/es', function () use ($app) {
	// on purpose not documented
	$app->post('/init/','es_init_post');
	// on purpose not documented
	$app->delete('/init/','es_init_delete');
});


function es_init_post() {
	$app = \Slim\Slim::getInstance();

	$hosts = array (TEMPR_ES_HOST);
	$client = Elasticsearch\ClientBuilder::create()
					->setHosts($hosts)
					->build();
	$params = [	'index' => 'cities' ];
	if ($client->indices()->exists($params)) {
		serve_json($app, new stdClass(), 200);
		return;
	}

	$params['body'] = '{
			"mappings": {
       			"cities": {
            		"properties": {
                		"id": {"type": "integer"},
            			"name": {"type": "string"},
                		"country": {"type": "string"},
                		"location": {"type": "geo_point"}
            		}
        		}
    		}
		}';
	$response = $client->indices()->create($params);

	$db = db_connect();

	$start = microtime(true);

	$stmt = $db->prepare('select * from cities limit 2',array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
	$stmt->execute();
	$i = 0;
	$k = 10000;
	$params = ['body'=>[]];
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		$params['body'][] = [
			'index' => [
				'_index' => 'cities',
				'_type' => 'cities',
				'_id' => $row['pk_city_id']
			]
		];
		$params['body'][] = [
			'name' => $row['name'],
			'country' => $row['country'],
			'location' => [ 'lat'=>$row['latitude'],'lon'=>$row['longitude'] ]
		];
		error_log(print_r($params,1));
		//print_r($response);
		++$i;
		if ($i>$k) {
			$responses = $client->bulk($params);
			$params = ['body'=>[]];
			unset($responses);
			$i = 0;
		}
	}
	if ($i>0) {
		$responses = $client->bulk($params);
		$params = ['body'=>[]];
		unset($responses);
		$i = 0;
	}

	$end = microtime(true);
	$time = $end - $start;
	error_log('script took ' . $time . ' seconds to execute with k = '.$k);

	$res = new stdClass();
	serve_json($app,$res, 200);
}


function es_init_delete() {
	$app = \Slim\Slim::getInstance();
	$hosts = array (TEMPR_ES_HOST);
	$client = Elasticsearch\ClientBuilder::create()
					->setHosts($hosts)
					->build();
	$deleteParams = [
		'index' => 'cities'
	];
	$response = $client->indices()->delete($deleteParams);
	serve_json($app, $response, 200);
}
