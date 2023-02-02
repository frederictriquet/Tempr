<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Infos extends Page {
	public function __construct() {
		// error_log('Loading Profiles Class');
		parent::__construct ();
	}
	public function index() {
		$query = $this->db->query( 'select pk_post_id, pp.creation_ts, pp.from_fk_user_id, 
				pp.to_fk_user_id, pp.to_fk_pending_user_id, pp.fk_devcity_id, pp.pending_reason, pu.* 
				from pending_posts pp
				join pending_users pu on pu.pk_user_id = pp.to_fk_pending_user_id 
				join users u on u.phone = pu.phone' );
		$phone = $query->result();

		$query = $this->db->query( 'select pk_post_id, pp.creation_ts, pp.from_fk_user_id, 
				pp.to_fk_user_id, pp.to_fk_pending_user_id, pp.fk_devcity_id, pp.pending_reason, pu.* 
				from pending_posts pp 
				join pending_users pu on pu.pk_user_id = pp.to_fk_pending_user_id 
				join users u on u.facebook_id = pu.facebook_id' );
		$fb = $query->result();

		$query = $this->db->query( 'select pp.from_fk_user_id as fk_user_id1, pp.to_fk_user_id as fk_user_id2 
				from pending_posts pp 
				where pp.pending_reason = B\'00010000\' 
				except 
				select fk_user_id1,fk_user_id2 from friendship_requests' );
		$friends_requests = $query->result();

		$query = $this->db->query( 'select u.pk_user_id, u.login, count(fr.fk_user_id1) as demandeur, count(fr2.fk_user_id2) 
				as demandey, count(pfr.from_fk_user_id) as inviteur from users u 
				left join friendship_requests fr on fr.fk_user_id1 = u.pk_user_id
				left join friendship_requests fr2 on fr2.fk_user_id2 = u.pk_user_id 
				left join pending_friendship_requests pfr on pfr.from_fk_user_id = u.pk_user_id 
				where not exists (select 1 from friendships f where f.fk_user_id1 = u.pk_user_id) 
				group by u.pk_user_id 
				order by u.pk_user_id' );
		$user_requests = $query->result();

		$query = $this->db->query( 'select pfr.*, pu.phone, pu.facebook_id 
				from pending_friendship_requests pfr 
				join pending_users pu on pu.pk_user_id = pfr.to_fk_pending_user_id 
				order by pfr.from_fk_user_id' );
		$user_pending = $query->result();

		$this->data ['main'] [] = 'infos';
		$this->data ['phone'] = $phone;
		$this->data ['fb'] = $fb;
		$this->data ['friends_requests'] = $friends_requests;
		$this->data ['user_requests'] = $user_requests;
		$this->data ['user_pending'] = $user_pending;
		$this->load_template ();
	}
}
