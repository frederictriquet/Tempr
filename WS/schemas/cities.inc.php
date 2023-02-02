<?php
require_once('conf/es.inc.php');

$app->group('/cities', function () use ($app) {
	// documented
	$app->get('/:lat/:lon','cities_pg_get');
	// on purpose not documented
	$app->get('/pg/:lat/:lon','cities_pg_get');
	// on purpose not documented
	$app->get('/es/:lat/:lon','cities_es_get');
});


function cities_pg_get($lat, $lon) {
	$app = \Slim\Slim::getInstance();

	$db = db_connect();

	$lat = (double)$lat;
	$lon = (double)$lon;

	$stmt = $db->prepare('select * from cities_get(:lat, :lon)');
	$stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
	$stmt->bindParam(':lon', $lon, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//error_log(print_r($res,1));
	serve_json($app,$res, 200);
}

/*
 SELECT cities.*, ST_AsEWKT(cities.geo), ST_Distance(geo, poi)/1000 AS distance_km
FROM cities, (select ST_MakePoint(63.077056,-179.413670)::geography as poi) as poi
WHERE ST_DWithin(geo, poi, 1000000)
ORDER BY ST_Distance(geo, poi) LIMIT 40;

-- Faches 50.600714, 3.064836
-- Bugnicourt 50.297400, 3.163453
*/


function cities_es_get($lat, $lon) {
	$app = \Slim\Slim::getInstance();

	$db = db_connect();

	$lat = (double)$lat;
	$lon = (double)$lon;

	$hosts = array (TEMPR_ES_HOST);
	$client = Elasticsearch\ClientBuilder::create()
					->setHosts($hosts)
					->build();
	$searchParams['index'] = 'cities';
	$searchParams['type']  = 'cities';
	$searchParams['body'] = '{
	  "sort" : [
	      {
	          "_geo_distance" : {
	              "location" : {
	                    "lat" : '.$lat.',
	                    "lon" : '.$lon.'
	              },
	              "order" : "asc",
	              "unit" : "km"
	          }
	      }
	  ],
	  "query": {
	    "filtered" : {
	        "query" : {
	            "match_all" : {}
	        },
	        "filter" : {
	            "geo_distance" : {
	                "distance" : "20km",
	                "location" : {
	                    "lat" : '.$lat.',
	                    "lon" : '.$lon.'
	                }
	            }
	        }
	    }
	  }
	}';

	$retDoc = $client->search($searchParams);

	$res  = array();
	if (array_key_exists('hits',$retDoc) && array_key_exists('hits', $retDoc['hits'])) {
		foreach ($retDoc['hits']['hits'] as $r) {
			$res[] = array(
					'country' => $r['_source']['country'],
					'pk_city_id'=> (int)$r['_id'],
					'name'=> $r['_source']['name'],
					'km'=>$r['sort'][0]
					);
		}
	}
	serve_json($app,$res, 200);
}
