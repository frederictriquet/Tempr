<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// page de partage de post via un SMS
class S extends Page {

	public function __construct() {
		parent::__construct();
	}
// https://www.tempr.me/s/yUPr8fu2pJkzmDa
	public function index($k) {
	    $r = $this->tools->new_redis(TEMPR_STORE_POSTSMS_DB);
	    $id_post = 0;
	    //error_log('la cle '.$k);
	    if ($r->exists($k)) {
	        //error_log('elle existe');
	        $id_post = $r->get($k);
	    //} else {
	    //    error_log('pas la');
	    }
	    //error_log('donc id post = '.$id_post);
		parent::page_post($id_post, 'from_filename_profile', 'view_decorated_pending_posts');

		$this->data['css'][] = 's.css';

		$this->data['main'][] = 's';

		$this->load_template();
	} 
}