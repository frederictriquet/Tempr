<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Metas extends Page {
	/*
	 * config of default settings
	*/
	public function index() {
		$this->_displayMetas(false);
	}

	public function edit() {
		$this->_displayMetas(true);
	}

	private function _displayMetas($is_editing) {
		$metas = $this->db->query('SELECT * from Metas ORDER BY name');
		$this->data['metas'] = $metas->result();

		$this->data['main'][] = 'metas_table';

		//$this->data['debug'][] = $_SERVER;

		$this->data['is_editing'] = $is_editing;

		$this->load_template();
	}

	// AJAX REQUESTS

	public function ajax_update($id, $what, $value) {
		$data = array($what => $value);
		$this->db->where('pk_meta_id = ', (int)$id);
		$this->db->update('metas', $data);
		echo 'OK';
	}
}
