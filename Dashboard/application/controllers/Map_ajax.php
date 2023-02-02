<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map_ajax extends AjaxPage {


	public function test($lat,$lon) {
		$this->data['main'][] = 'htags';

		$lat = (double)$lat;
		$lon = (double)$lon;

		$stmt = $this->db->conn_id->prepare('select * from cities_get(:lat, :lon)');
		$stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
		$stmt->bindParam(':lon', $lon, PDO::PARAM_STR);
		$stmt->execute();
		$this->data['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$this->serve_data();
		//error_log('data served');
		//error_log(print_r($retDoc,1));
	}

	public function test_es($lat,$lon) {
		$this->data['main'][] = 'htags';

		$hosts = array (TEMPR_ES_HOST);
		$client = Elasticsearch\ClientBuilder::create()
						->setHosts($hosts)
						->build();
		//$client = new Elasticsearch\Client($params);

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
		$this->data['data'] = $res;
		$this->serve_data();
		//error_log('data served');
		//error_log(print_r($retDoc,1));
	}
}
