<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Htags_ajax extends AjaxPage {

	public function test($prefix) {
		$this->data['main'][] = 'htags';

		$params['hosts'] = array (TEMPR_ES_HOST);
		error_log('Htags_ajax::test : '.$prefix); // FIXME
		$client = new Elasticsearch\Client($params);
		$searchParams['index'] = 'htags';
		$searchParams['type']  = 'htags';
		$searchParams['body']['query']['bool']['must']['prefix']['htags.tag'] = $prefix;
		$retDoc = $client->search($searchParams);

		$this->data['data'] = $retDoc;
		$this->serve_data();
		//error_log('data served');
		//error_log(print_r($retDoc,1));
	}
}
