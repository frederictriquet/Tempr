<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends Page {

	public function index() {
		$this->data['main'][] = 'map';
		$this->load_template();
	}
}
