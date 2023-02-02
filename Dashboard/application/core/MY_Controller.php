<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// extending CI_Controller multiple times, see:
// http://stackoverflow.com/questions/8004385/codeigniter-my-controller-is-it-only-possible-to-extend-core-once

class MY_Controller extends CI_Controller {
	public function __construct() {
		//error_log('Loading MY_Controller Class');
		parent::__construct();
	}
}

class Page extends MY_Controller {

	public $data = array();

	public function __construct() {
		//error_log('Loading Page Class');
		parent::__construct();
		$this->initialize();
	}

	public function initialize() {
		$this->data['css'][] = 'reset.css';
		$this->data['css'][] = 'generic.css';
		$this->data['css'][] = 'jquery.dataTables.css';
		$this->data['css'][] = 'datatables.css';
		$this->data['css'][] = 'jquery.toast.min.css';
		$this->data['css'][] = 'waiting.css';
		$this->data['css'][] = 'menu.css';
		$this->data['css'][] = 'font-awesome.min.css';
		$this->data['css'][] = 'popup.css';
		$this->data['css'][] = 'debug.css';

		$this->data['css'][] = 'bootstrap.min.css';
		$this->data['css'][] = 'bootstrap-theme.min.css';
		$this->data['css'][] = 'my.css';

		$this->data['js'][] = 'jquery-3.0.0.min.js';
		$this->data['js'][] = 'jquery.dataTables.min.js';
        //$this->data['js'][] = 'jquery.toast.min.js';
		$this->data['js'][] = 'jquery.waiting.min.js';
		$this->data['js'][] = 'jquery.popup.min.js';

		$this->data['js'][] = 'bootstrap.min.js';

		$this->data['js'][] = 'last.js';

		// Title, Metadescription et Metakeywords par défaut :
		$this->data['title_default'] = $this->config->item('site_name');
		$this->data['title'] = $this->data['title_default'].'';
		$this->data['meta_description'] = '';
		$this->data['meta_keywords'] = '';
		// Contenu
		$this->data['top'] = array();
		$this->data['main'] = array();
		$this->data['bottom'] = array();

		//$this->data['ariane'] = array();
		//$this->data['ariane'][0] = new stdClass();
		//$this->data['ariane'][0]->label = 'Accueil';
		//$this->data['ariane'][0]->link = site_url();

	}

	public function load_template() {

		$this->data['top'][] = 'sub/menu';

		$template = '';

		$template.= $this->load->view('template/header', $this->data, TRUE);
		$template.= $this->load->view('template/main', $this->data, TRUE);
		$template.= $this->load->view('template/footer', $this->data, TRUE);

		if (isset($this->data['debug']))
			$template.= $this->load->view('debug', $this->data, TRUE);

		echo $template;
	}

	public function load_ajax_template() {
		$template = '';

		$template.= $this->load->view('template/ajax', $this->data, TRUE);

		echo $template;
	}

	public function load_raw_template() {
	    $this->load->view($this->data['raw'], $this->data);
	}
}


class AjaxPage extends MY_Controller {

	public $data = array();

	public function __construct() {
		error_log('Loading AjaxPage Class');
		parent::__construct();
		$this->initialize();
	}

	public function initialize() {
		error_log('AjaxPage::initialize');
	}

	public function serve_data() {
		header('Content-Type: application/json');
		//$this->load->view('template/ajax', $this->data, FALSE);

		//if (isset($this->data['debug']))
		//	$template.= $this->load->view('debug', $this->data, TRUE);

		echo json_encode($this->data['data']);
	}
}
