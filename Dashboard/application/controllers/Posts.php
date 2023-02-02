<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Posts extends Page {

	public function __construct() {
		//error_log('Loading Profiles Class');
		parent::__construct();
	}

	public function index($user_id=null) {
		$query = $this->db->query('select * from view_decorated_posts order by creation_ts desc');
		if ($user_id !== null) {
			$user_id = (int) $user_id;
			if ($user_id>0)
				$query = $this->db->query('select * from view_decorated_posts where to_fk_user_id = ? order by creation_ts desc', array($user_id));
		}
		$r = $query->result();

		$this->s3tools->init();

		//foreach ($r as &$obj) {
			//$obj->url_media = $this->s3tools->resolve_S3_filename($obj->filename);
		//}
		$this->data['main'][] = 'posts_table';
		$this->data['posts'] = $r;
		//$this->data['debug'] = print_r($r,1);
		$this->load_template();
	}
}
