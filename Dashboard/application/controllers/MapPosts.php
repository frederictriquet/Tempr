<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MapPosts extends Page {

	public function index() {
		$this->data['main'][] = 'mapPosts';
		$this->load_template();
	}

	public function ajax_posts() {
		$posts = $this->db->conn_id->prepare('SELECT COUNT(*), c.latitude, c.longitude FROM posts p
				INNER JOIN devcities dc ON p.fk_devcity_id = dc.pk_devcity_id
				INNER JOIN cities c ON dc.fk_city_id = c.pk_city_id
				GROUP BY p.fk_devcity_id, dc.locality, c.latitude, c.longitude');
		$posts->execute();

		header('application/json');
		echo json_encode($posts->fetchAll(PDO::FETCH_ASSOC));
	}

	public function ajax_posts3($lon=null,$LON=null,$lat=null,$LAT=null) {
	    // longitude min, longitude max, latitude min, latitude max
	    error_log(print_r([$lon,$LON,$lat,$LAT],1));
	    if (empty($lon)) {
    		$posts = $this->db->conn_id->prepare('SELECT st_y(geo::geometry) as latitude, st_x(geo::geometry) as longitude FROM posts p
    				WHERE geo IS NOT NULL');
	    } else {
	        $posts = $this->db->conn_id->prepare('SELECT st_y(geo::geometry) as latitude, st_x(geo::geometry) as longitude FROM posts p
	            				WHERE geo IS NOT NULL
	                            AND st_makeenvelope(:left,:bottom,:right,:top, 4326) ~ geo::geometry
	                ');
	        $posts->bindValue(':left', $lon);
	        $posts->bindValue(':bottom', $lat);
	        $posts->bindValue(':right', $LON);
	        $posts->bindValue(':top', $LAT);
	    }
		$posts->execute();
		error_log($posts->rowCount()+' posts');
		header('application/json');
		echo json_encode($posts->fetchAll(PDO::FETCH_ASSOC));
	}
}