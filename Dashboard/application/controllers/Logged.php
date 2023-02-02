<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Logged extends Page {

	public function __construct() {
		//error_log('Loading Profiles Class');
		parent::__construct();
	}

	public function index() {
		$query = $this->db->query('select fk_user_id, token, valid_until_ts from oauth_tokens order by valid_until_ts desc');
		$r = $query->result();

		//$this->data['debug'] = print_r($r,1);
		$this->data['users'] = $r;
		$this->data['main'][] = 'logged_table';
		$this->load_template();
	}
}
