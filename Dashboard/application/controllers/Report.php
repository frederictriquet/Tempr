<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Report extends Page {
	public function __construct() {
		parent::__construct ();
	}
	public function index() {
		$res = $this->db->query('select rp.*, 
				vdp.body,
				vdp.from_fk_user_id,
				vdp.from_firstname,
				vdp.from_lastname,
				vdp.to_fk_user_id,
				vdp.to_firstname,
				vdp.to_lastname,
 				vdp.tag1,
				vdp.tag2,
				vdp.tag3,
				vdp.filename,
				vdp.filename_vid,
				u.firstname,
				u.lastname,
				u.login,
				uf.login as from_login,
				ut.login as to_login
				from reports_posts rp 
				left join view_decorated_posts vdp 
				on vdp.pk_post_id = rp.fk_post_id
				inner join users u
				on u.pk_user_id = rp.fk_user_id
				inner join users uf
				on uf.pk_user_id = vdp.from_fk_user_id
				inner join users ut
				on ut.pk_user_id = vdp.to_fk_user_id
				');
		
		$report_post = $res->result();
		$this->s3tools->init();
		foreach ( $report_post as $r ) {
			if (!empty ($r->filename))
				$r->filename = $this->s3tools->resolve_S3_filename($r->filename);
			if (!empty ($r->filename_vid ))
				$r->filename_vid = $this->s3tools->resolve_S3_filename($r->filename_vid);
		}
		$this->data['report_post'] = $report_post;
		
		$res = $this->db->query('select rc.*, 
				c.body,
				c.fk_post_id,
				c.from_fk_user_id,
				u.firstname,
				u.lastname,
				u.login,
				uf.login as from_login,
				uf.firstname as from_firstname,
				uf.lastname as from_lastname
				from reports_comments rc
				left join comments c
				on c.pk_comment_id = rc.fk_comment_id
				inner join users u
				on u.pk_user_id = rc.fk_user_id
				inner join users uf
				on uf.pk_user_id = c.from_fk_user_id
				');
		
		$report_comments = $res->result();
		$this->data['report_comments'] = $report_comments;

		$this->data['main'][] = 'report';
		$this->load_template();
	}
	public function ajax_validate($type, $id_type, $id_user) {
		if ($type == "post") {
			$delete_post = $this->db->conn_id->prepare("DELETE FROM posts WHERE pk_post_id = :id_type");
			$delete_post->bindParam(':id_type', $id_type, PDO::PARAM_INT);
			$delete_post->execute();
			
			$delete_report = $this->db->conn_id->prepare("DELETE FROM reports_posts WHERE fk_post_id = :id_type");
			$delete_report->bindParam(':id_type', $id_type, PDO::PARAM_INT);
			$delete_report->execute();
		}
		elseif ($type == "comment") {
			$delete_post = $this->db->conn_id->prepare("DELETE FROM comments WHERE pk_comment_id = :id_type");
			$delete_post->bindParam(':id_type', $id_type, PDO::PARAM_INT);
			$delete_post->execute();
				
			$delete_report = $this->db->conn_id->prepare("DELETE FROM reports_comments WHERE fk_post_id = :id_type");
			$delete_report->bindParam(':id_type', $id_type, PDO::PARAM_INT);
			$delete_report->execute();
		}
		$cmp_report = $this->db->conn_id->prepare("SELECT * from user_report(:id_user)");
		$cmp_report->bindParam(':id_user', $id_user, PDO::PARAM_INT);
		$cmp_report->execute();
		echo 'OK Validate';
	}
	
	public function ajax_refuse($type, $id) {
		if ($type == "post")
			$delete_report = $this->db->conn_id->prepare("DELETE FROM reports_posts WHERE fk_post_id = :id");
		elseif ($type == "comment")
			$delete_report = $this->db->conn_id->prepare("DELETE FROM reports_comments WHERE fk_comment_id = :id");
		$delete_report->bindParam(':id', $id, PDO::PARAM_INT);
		$delete_report->execute();
		
		echo 'OK Refuse';
	}
}
?>