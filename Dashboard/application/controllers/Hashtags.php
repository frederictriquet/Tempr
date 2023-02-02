<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Hashtags extends Page {

	public function __construct() {
		//error_log('Loading Profiles Class');
		parent::__construct();
	}

	public function index() {
		$query = $this->db->query('select h.*, count(hl.*) as nb 
				from htags h 
				join posts_htags ph on ph.fk_htag_id = h.pk_htag_id 
				join htags_likes hl on hl.fk_post_id = ph.fk_post_id and hl.ck_seq_id = ph.ck_seq_id 
				group by pk_htag_id 
				order by nb desc
				limit 10');
		$rank_tags = $query->result();
		$this->data['rank_tags'] = $rank_tags;

		$query = $this->db->query('select count(*) from htags');
		$count_tags = $query->result();
		$this->data['count_tags'] = $count_tags;

		$query = $this->db->query('select count(*) from htags_likes');
		$count_likes = $query->result();
		$this->data['count_likes'] = $count_likes;
		
		$this->data ['main'] [] = 'hashtags';
		$this->load_template ();
	}
}
