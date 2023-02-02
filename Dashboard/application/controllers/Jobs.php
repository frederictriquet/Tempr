<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Jobs extends Page {
	/*
	 * config of default settings
	*/
	public function index() {
		$this->data['main'][] = 'mq_send';
		$this->_displayParameter(false);
	}

	public function edit() {
		$this->_displayParameter(true);
	}

	private function _displayParameter($is_editing) {
		$jobs = $this->db->query('SELECT * from jobs ORDER BY name');
		$this->data['jobs'] = $jobs->result();
		$this->data['activity_types'] = array('none','once','active');

		$this->data['main'][] = 'jobs_table';

		//$this->data['debug'] = print_r($jobs,1);

		$this->data['is_editing'] = $is_editing;

		$this->load_template();
	}

	// AJAX REQUESTS

	// l'update d'une valeur de conf
	public function ajax_update() {
		$id = $this->input->post('id');
		$key = $this->input->post('key');
		$value = $this->input->post('value');
		$data = array($key => $value);
		$where = 'pk_job_id = '.(int)$id;
		$this->db->query(
			$this->db->update_string('jobs', $data, $where)
		);
	}

	public function ajax_mq($x) {
		if ($x === '0') {
			$m = '{"action":"sendmail","subaction":"resetpass","to":"frederic.triquet+tempr@gmail.com","token":"38cc31cdaaff8bbcc04bb657f9524ce0eb8941d93a8c6b40e5e79b274e5e445c"}';
			$type = 'mail';
		} else if ($x === '1') {
			$m = '{"action":"sendsms","to":"+33689824777","code":"1234"}';
			$type = 'sms';
		} else return;
		$connection = new AMQPStreamConnection(TEMPR_MQ_HOST, 5672, 'lapin', 'lapin');
		$channel = $connection->channel();
		$channel->queue_declare('hello', false, false, false, false);

		$msg = new AMQPMessage($m);
		$channel->basic_publish($msg, '', 'hello');
		$channel->close();
		$connection->close();
	}


}
