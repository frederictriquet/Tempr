<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// url de confirmation de phone
class C extends Page {

	public function __construct() {
		parent::__construct();
	}

// https://www.tempr.me/c/yUPr8fu2pJkzmDa
	public function index($k) {
	    $r = $this->tools->new_redis(TEMPR_STORE_CONFIRMPHONE_DB);
	    if ($r->exists($k)) {
	        $data = $r->get($k);
	        $d = json_decode($data);
	        $query = $this->db->conn_id->prepare('select * from user_confirm_phone_by_phone(:id,:phone)');
	        $query->bindValue(':id', $d->id, PDO::PARAM_INT);
	        $query->bindValue(':phone', $d->phone, PDO::PARAM_STR);
	        $query->execute();
	        if ($query->rowCount() == 1) {
	            $query = $query->fetchAll(PDO::FETCH_NUM);
	        }
    		$this->data['main'][] = 'phoneconfirmdone';
    		$r->delete($k);
	    } else {
	        $this->data['main'][] = 'phoneconfirmnotdone';
	    }
	    $this->data['css'][] = 'tempr.css';

		$this->load_template();
	} 
}