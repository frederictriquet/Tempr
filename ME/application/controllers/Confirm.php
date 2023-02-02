<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Confirm extends Page {

	public function __construct() {
		parent::__construct();
	}

	// see also routes.php for url routing
	public function index($token) {
		$query = $this->db->query(
				'select user_confirm_email(?)',
				array($token));
		$query = $this->db->query('select * from users');
		$this->debug[] = $query->result();
		$this->data['main'][] = 'mailconfirmdone';
		$this->load_template();
	}
}
