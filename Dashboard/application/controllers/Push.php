<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Push extends Page {

	public function __construct() {
		//error_log('Loading Profiles Class');
		parent::__construct();
	}

	public function index() {
		$query = $this->db->query('select pk_user_id, firstname, lastname, ios_id from iosdevices join users u on u.pk_user_id = fk_user_id order by fk_user_id');
		$r = $query->result();

		$this->data['push'] = $r;
		$this->data['main'][] = 'push_table';
		//$this->data['debug'] = print_r($r,1);
		$this->load_template();
	}

	public function send() {
		$query = $this->db->query('select fk_user_id, ios_id from iosdevices group by fk_user_id, ios_id');
		if ($user_id !== null) {
			$user_id = (int) $user_id;
			if ($user_id>0)
				$query = $this->db->query('select * from iosdevices where fk_user_id = ?', array($user_id));
		}
		$r = $query->result();
	
		$this->data['debug'] = print_r($r,1);
		$this->load_template();
	}

	// AJAX requests
	public function ajax_send($id) {
		$obj = (object)[
			'type'=>'system',
			'to_id'=>(int)$id
		];
		$this->tools->send_to_rabbit('push',$obj);
		echo 'OK';
	}
}
