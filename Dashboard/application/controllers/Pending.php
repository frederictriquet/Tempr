<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Pending extends Page {

	public function __construct() {
		//error_log('Loading Profiles Class');
		parent::__construct();
	}

	public function index() {
		$query = $this->db->query('select * from pending_users order by pk_user_id');
		$users = $query->result();

		$query = $this->db->query('select * from pending_friendship_requests order by from_fk_user_id');
		$friends = $query->result();
		
		$query = $this->db->query('select * from pending_posts order by from_fk_user_id');
		$posts = $query->result();
		
		$this->data['main'][] = 'pending';
		$this->data['users'] = $users;
		$this->data['friends'] = $friends;
		$this->data['posts'] = $posts;
		$this->load_template();
	}
}
