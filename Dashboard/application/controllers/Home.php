<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Page {

	public function __construct() {
		error_log('Loading Home Class');
		parent::__construct();
	}

	public function index() {
		$query = $this->db->query('select * from metas');
		$r= $query->result();
		//print_r($r);
		$data = $r;

		$this->data['main'][] = 'home';

		$this->load_template();
	}
}
