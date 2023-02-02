<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reset extends Page {

	public function __construct() {
		parent::__construct();
	}

	// see also routes.php for url routing
	public function index($token) {
		// check if the token is valid
		$query = $this->db->query(
				'select firstname, lastname from users
				join password_resets on pk_user_id = fk_user_id
				where token = ? limit 1',
				array($token));
		if ($query->num_rows() > 0) {
			$r = $query->result();
			$this->data['user'] = $r[0];
			$this->data['token'] = $token;
			$this->load->library('form_validation');
			$config = array(
					array('field'=>'password', 'label'=>'Password',
							'rules'=>'trim|required|min_length[8]|max_length[20]|matches[confirmpassword]|callback_password_check'),
					array('field'=>'confirmpassword', 'label'=>'Confirm Password',
							'rules'=>'trim|required|min_length[8]|max_length[20]|callback_password_check')
			);
			$this->form_validation->set_message('password_check', 'unsafe password');
			$this->form_validation->set_rules($config);
			if ($this->form_validation->run() === FALSE) {
				$this->data['css'][] = 'form.css';
				$this->data['main'][] = 'reset';
			} else {
				$password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
				$query = $this->db->query('select user_update_password(?, ?)',
						array($token, $password));
				error_log('updated');
				redirect('done');
			}
			$this->load_template();
		} else {
			redirect('/');
			return;
		}
	}

	function password_check($password) {
		//error_log('check '.$password. ' '.preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password));
		//return False;
		return strlen($password) > 7;
		//return (1 === preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password));
	}
}
