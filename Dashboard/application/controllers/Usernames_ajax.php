<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usernames_ajax extends AjaxPage {

	public function test($prefix) {
		$prefix = urldecode($prefix);
		$this->data['main'][] = 'usernames';

		$params['hosts'] = array (TEMPR_ES_HOST);
		error_log('Usernames_ajax::test : '.$prefix); // FIXME
		$client = new Elasticsearch\Client($params);
		$searchParams['index'] = 'users';
		$searchParams['type']  = 'names';
		//$searchParams['body']['query']['bool']['must']['prefix']['_all'] = $prefix;

/*		$searchParams['body'] =
		'{
			"query":{
				"bool":{
					"must":{
						"prefix":{
							"firstname":"'.$prefix.'"
						}
					}
				}
			}
		}';
*/
/*		$searchParams['body'] =
		'{
			"query":{
						"prefix":{
							"firstname":"'.$prefix.'"
						}
			}
		}';
*/
/*		$searchParams['body'] =
		'{
			"query": {
				"multi_match" : {
					"query":      "'.$prefix.'",
					"type":       "best_fields",
					"fields":     [ "firstname", "lastname" ],
					"tie_breaker": 0.3
				}
			}
		}';
*/
/*
		$searchParams['body'] =
		'{
			"query":{
				"match":{
					"firstname":{
						"query":"'.$prefix.'"
					}
				}
			},
			"highlight": {
				"pre_tags":["<u>"],
				"post_tags":["</u>"],
				"order": "score",
				"fields":{
					"firstname" : {
		                "fragment_size" : 150,
        		        "number_of_fragments" : 3,
                		"highlight_query": {
                    		"bool": {
                        		"must": {
                            		"match": {
                                		"firstname": {
                                  			"query": "'.$prefix.'"
										}
									}
								},
								"should": {
									"match_phrase": {
										"firstname": {
											"query": "'.$prefix.'",
											"phrase_slop": 1,
											"boost": 10.0
										}
									}
								},
								"minimum_should_match": 0
							}
                		}
					}
				}
			}
		}';
*/


/*		$searchParams['body'] ='{
			"query": {
				"bool": {
					"should": [
						{"match": {"firstname":    "'.$prefix.'" }},
						{"match": {"lastname":    "'.$prefix.'" }}
					]
				}
			}
		}';
*/

/*		$searchParams['body'] ='{
			"query": {
				"multi_match": {
					"query":       "'.$prefix.'",
					"type":        "most_fields",
					"fields":      [ "firstname", "lastname" ]
				}
			}
		}';
*/

		$searchParams['body'] ='{
			"query": {
				"bool": {
					"should": [
						{"prefix": {"firstname":    "'.$prefix.'" }},
						{"prefix": {"lastname":    "'.$prefix.'" }}
					]
				}
			},
			"highlight": {
				"pre_tags": ["<u>"],
				"post_tags": ["</u>"],
				"fields": {
						"content": {
					"firstname": {
							"force_source" : true,
							"index_options": "offsets"
						}
					},
					"lastname": {
					}
				}
			}
		}';

		$retDoc = $client->search($searchParams);

		$this->data['data'] = $retDoc;
		$this->serve_data();
		//error_log('data served');
		//error_log(print_r($retDoc,1));
	}
}
