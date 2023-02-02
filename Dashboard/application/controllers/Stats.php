<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Stats extends Page {
	public function __construct() {
		parent::__construct ();
		$CI = &get_instance ();
		$this->db2 = $CI->load->database ( 'stats', TRUE );
	}
	public function index() {
		
		// nb inscription
		$signup = $this->db2->query('select pk_signup_date, nb_signups_total from signups order by pk_signup_date');
		$signup = $signup->result();
		$posts = $this->db2->query('select pk_signup_date, nb_signups_total/2 as nb_posts from signups order by pk_signup_date');
		$posts = $posts->result();

		$curr_date = date('Y-m-d');
		$one_week_ago = date('Y-m-d',strtotime("-7 days",strtotime($curr_date)));
		$two_weeks_ago = date('Y-m-d',strtotime("-14 days",strtotime($curr_date)));
		$one_month_ago = date('Y-m-d',strtotime("-30 days",strtotime($curr_date)));
		$month = array ("user" => [], "day" => [], "posts" => []);
		
		foreach ( $signup as $r ) {			
// 			if (date ('Y-m-d H:s:i',strtotime("-1 month",strtotime($curr_date)) < $r->pk_signup_date)
// 			        && $r->pk_signup_date <= $curr_date) {
				array_push ( $month ["user"], $r->nb_signups_total );
				array_push ( $month ["day"], $r->pk_signup_date );
// 			}
		}
		foreach ( $posts as $r ) {
		    array_push ( $month ["posts"], $r->nb_posts );
		}
		
		$this->data['month'] = $month;


		// nb friends
		$friends = $this->db2->query ( 'select ck_date, nb_friends_today from friends' );
		$friends = $friends->result ();
		//$this->data['debug'] = $friends;

		$fr_0 = array(0,0,0,0);
		$fr_1_5 = array(0,0,0,0);
		$fr_6_10 = array(0,0,0,0);
		$fr_11_15 = array(0,0,0,0);
		$fr_16_ = array(0,0,0,0);

		foreach ( $friends as $f ) {
		    if ($f->ck_date == $curr_date) {
		        if ($f->nb_friends_today == 0)
		            $fr_0[0]++;
		        else if ($f->nb_friends_today <= 5)
		            $fr_1_5[0]++;
		        else if (6 <= $f->nb_friends_today && $f->nb_friends_today <= 10)
		            $fr_6_10[0]++;
	            else if (11 <= $f->nb_friends_today && $f->nb_friends_today <= 15)
		            $fr_11_15[0]++;
                else
		            $fr_16_[0]++;
		    }

		    if ($one_week_ago == $f->ck_date) {
		        if ($f->nb_friends_today == 0)
		            $fr_0[1]++;
		        else if ($f->nb_friends_today <= 5)
		            $fr_1_5[1]++;
		        else if (6 <= $f->nb_friends_today && $f->nb_friends_today <= 10)
		            $fr_6_10[1]++;
		        else if (11 <= $f->nb_friends_today && $f->nb_friends_today <= 15)
		            $fr_11_15[1]++;
		        else
		            $fr_16_[1]++;
		    }

		    if ($two_weeks_ago == $f->ck_date) {
		        if ($f->nb_friends_today == 0)
		            $fr_0[2]++;
		        else if ($f->nb_friends_today <= 5)
		            $fr_1_5[2]++;
		        else if (6 <= $f->nb_friends_today && $f->nb_friends_today <= 10)
		            $fr_6_10[2]++;
		        else if (11 <= $f->nb_friends_today && $f->nb_friends_today <= 15)
		            $fr_11_15[2]++;
		        else
		            $fr_16_[2]++;
		    }
		
		    if ($one_month_ago == $f->ck_date) {
		        if ($f->nb_friends_today == 0)
		            $fr_0[3]++;
		        else if ($f->nb_friends_today <= 5)
		            $fr_1_5[3]++;
		        else if (6 <= $f->nb_friends_today && $f->nb_friends_today <= 10)
		            $fr_6_10[3]++;
		        else if (11 <= $f->nb_friends_today && $f->nb_friends_today <= 15)
		            $fr_11_15[3]++;
		        else
		            $fr_16_[3]++;
		    }
		}

		$this->data ['fr_0'] = $fr_0;
		$this->data ['fr_1_5'] = $fr_1_5;
		$this->data ['fr_6_10'] = $fr_6_10;
		$this->data ['fr_11_15'] = $fr_11_15;
		$this->data ['fr_16_'] = $fr_16_;
		
		$this->data ['main'] [] = 'stats_table';
		$this->load_template ();
	}
}