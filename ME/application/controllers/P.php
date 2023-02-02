<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// page de partage de post
class P extends Page {

	public function __construct() {
		parent::__construct();
	}

	public function index($id_post) {
		parent::page_post($id_post, 'to_filename_profile');
		
		$this->data['css'][] = 'p.css';

		$this->data['main'][] = 'p';

		$this->load_template();
	}
}