<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parameters extends Page {
	/*
	 * config of default settings
	*/
	public function index() {
		$this->_displayParameter(false);
	}

	public function edit() {
		$this->_displayParameter(true);
	}

	private function _displayParameter($is_editing) {
		$parameter = $this->db->query('SELECT * from parameters ORDER BY name');
		$this->data['parameter'] = $parameter->result();

		$this->data['main'][] = 'parameters_table';

		//$this->data['debug'] = print_r($parameter,1);

		$this->data['is_editing'] = $is_editing;

		$this->load_template();
	}

	// AJAX REQUESTS

	// l'update d'une valeur de conf
	public function ajax_update($id, $value) {
		$data = array('value' => $value);
		$where = 'pk_parameter_id = '.(int)$id;
		$this->db->query(
			$this->db->update_string('parameters', $data, $where)
		);
		echo 'OK';
	}


}
