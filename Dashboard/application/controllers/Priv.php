<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Priv extends Page {

	public function __construct() {
		//error_log('Loading Profiles Class');
		parent::__construct();
	}

	public function index() {
		$query = $this->db->query('select * from oauth_tokens');
		$r = $query->result();

		//$this->data['main'][] = 'profiles';
		$this->data['debug'] = print_r($r,1);
		$this->load_template();
	}

	public function media() {
		$this->s3tools->init();
		$query = $this->db->query('select * from medias order by creation_ts desc');
		$r = $query->result();
		foreach ($r as &$obj) {
			$obj->url = '<a href="'.$this->s3tools->resolve_S3_filename($obj->filename).'">url</a>';
		}
		$this->data['debug'] = print_r($r,1);
		$this->load_template();
	}

	public function newtoken($id, $token) {
		$id = (int)$id;
		$data = array(
				'fk_user_id' => $id,
				'token' => $token,
				'refresh_token' => $token,
				'valid_until_ts' => '2020-01-01 10:08:29.983839'
		);
		$this->db->insert('oauth_tokens', $data);
		redirect('/Priv');
	}

	public function refreshtoken($token) {
		$data = array('valid_until_ts' => '2020-01-01 10:08:29.983839');
		$this->db->where('token = ', $token);
		$this->db->update('oauth_tokens', $data);
		redirect('/Priv');
	}

}
