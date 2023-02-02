<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends Page {

	public function index() {
		$this->data['main'][] = 'search';
		$this->load_template();
	}
}
