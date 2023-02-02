<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Profiles extends Page {
	
	var $perPage = 30;
	
    public function __construct() {
        //error_log('Loading Profiles Class');
        parent::__construct();
    }

    protected function get_users_($limit, $offset) {
		$users = $this->db->conn_id->prepare("select u.pk_user_id, u.login, u.firstname, u.lastname, u.phone, u.phone_confirmed, u.signup_date,
                u.email,
                u.password IS NOT NULL has_mail,
                u.facebook_id,
                u.lang, u.likes,
                nb_friends,
                nb_posts_sent,
                nb_posts_received,
                nb_requests,
                nb_requested,
                nb_pending_requests,
                likes_sent,
                last_seen,
                nb_push
                from users u
                left join (
                    select f.fk_user_id1, count(*) as nb_friends
                        from friendships f
                        group by f.fk_user_id1
                ) f_ on f_.fk_user_id1 =  u.pk_user_id
                left join (
                    select p1.from_fk_user_id, count(*) as nb_posts_sent
                        from posts p1
                        group by p1.from_fk_user_id
                ) p1_ on p1_.from_fk_user_id =  u.pk_user_id
                left join (
                    select p2.to_fk_user_id, count(*) as nb_posts_received
                        from posts p2
                        group by p2.to_fk_user_id
                ) p2_ on p2_.to_fk_user_id =  u.pk_user_id
                left join (
                    select fr.fk_user_id1, count(*) as nb_requests
                        from friendship_requests fr
                        group by fr.fk_user_id1
                ) fr_ on fr_.fk_user_id1 =  u.pk_user_id
                left join (
                    select fr2.fk_user_id2, count(*) as nb_requested
                        from friendship_requests fr2
                        group by fr2.fk_user_id2
                ) fr2_ on fr2_.fk_user_id2 =  u.pk_user_id
                left join (
                    select pfr.from_fk_user_id, count(*) as nb_pending_requests
                        from pending_friendship_requests pfr
                        group by pfr.from_fk_user_id
                ) pfr_ on pfr_.from_fk_user_id =  u.pk_user_id
                left join (
                    select l.fk_user_id, count(*) as likes_sent
                        from htags_likes l
                        group by l.fk_user_id
                ) l_ on l_.fk_user_id =  u.pk_user_id
                left join (
                    select u.pk_user_id, case when u.last_hello='-infinity'::timestamp
                        then -1 else extract(epoch from (now()-last_hello))/3600/24 end as last_seen
                        from users u
                        group by u.pk_user_id
                    ) u_ on u_.pk_user_id = u.pk_user_id
                left join (
                    select t.fk_user_id, count(*) as nb_push
                        from iosdevices t
                        group by t.fk_user_id
                    ) t_ on t_.fk_user_id = u.pk_user_id
                order by u.pk_user_id limit :limit offset :offset");

  		$users->bindValue(':limit', $limit, PDO::PARAM_INT);
        $users->bindValue(':offset', $offset, PDO::PARAM_INT);
        $users->execute();
        $users = $users->fetchAll(PDO::FETCH_ASSOC);

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        foreach ($users as &$u) {
            if (!empty($u['phone']))  {
                $p = $phoneUtil->parse($u['phone'], 'ZZ');
                $u['phone_country'] = $phoneUtil->getRegionCodeForNumber($p);
            } else $u['phone_country'] = '';
            if (empty($u['nb_posts_sent'])) $u['nb_posts_sent'] = 0;
            if (empty($u['nb_posts_received'])) $u['nb_posts_received'] = 0;
            if (empty($u['nb_friends'])) $u['nb_friends'] = 0;
            if (empty($u['nb_requests'])) $u['nb_requests'] = 0;
            if (empty($u['nb_requested'])) $u['nb_requested'] = 0;
            if (empty($u['nb_pending_requests'])) $u['nb_pending_requests'] = 0;
            if (empty($u['likes_sent'])) $u['likes_sent']= 0;
            if ($u['last_seen'] == -1) {
                $u['last_seen'] = '?';
                $u['last_seen_color'] = 'orange';
            } else {
                $u['last_seen'] = (int)$u['last_seen'];
                $u['last_seen_color'] = ($u['last_seen']>7) ? 'red' : 'green';
            }
        }
        return $users;
    }
    
    public function limitPage($limit) {
        error_log('limitPage('.$limit.')');
    	$query = $this->db->query('select count(*) from users');
    	$total_users = $query->result();
    	$this->nbUsers = $total_users[0]->count;
    	$nbPages = ceil($this->nbUsers / $limit);
    	return ($nbPages);
    }
    
    public function index($limit = 0, $offset = 0) {
        if ($limit > 0)
            $this->perPage = $limit;
        $users = $this->get_users_($this->perPage, $offset);
		if ($offset > 0) {
			$index[0]['page'] = ceil(($offset + 1) / $this->perPage) - 1;
			$index[0]['display'] = "&lt;";
			$index[0]['limit'] = $this->perPage;
			$index[0]['offset'] = $offset - $this->perPage;
		}
        $nbPage = $this->limitPage($this->perPage);
		for ($i = 1; $i <= $nbPage; $i++) {
			$index[$i]['page'] = $i;
			$index[$i]['display'] = $i;
			$index[$i]['limit'] = $this->perPage;
			$index[$i]['offset'] = $this->perPage * ($i - 1);
		}
		if (ceil(($offset + 1) / $this->perPage) < $nbPage) {
			$index[$i + 1]['page'] = ceil(($offset + 1) / $this->perPage) + 1;
			$index[$i + 1]['display'] = "&gt;";
			$index[$i + 1]['limit'] = $this->perPage;
			$index[$i + 1]['offset'] = $this->perPage + $offset;
		}
		$this->data['currentPage'] = ceil(($offset + 1) / $this->perPage);
    	$this->data['nbUsers'] = $this->nbUsers;
    	$this->data['index'] = $index;
    	$this->data['css'][] = 'profiles.css';
        $this->data['main'][] = 'profiles';
        $this->data['users'] = $users;
        //$this->data['debug'] = print_r($users,1);
        $this->load_template();
    }

    public function csv() {
        $this->data['users'] = $this->get_users_(10000,0); // TODO FIXME
        $this->data['raw'] = 'csv/profiles';
        $this->load_raw_template();
    }

    public function ajax_pagination($page, $display, $limit, $offset) {
    	$users = $this->get_users_($limit, $offset);
    	$this->data['users'] = $users;
    	if ($page > 1) {
    		$index[0]['page'] = $page - 1; 
    		$index[0]['display'] = "&lt;";
    		$index[0]['limit'] = $limit; 
    		$index[0]['offset'] = $offset - $limit;
    	}
    	$nbPage = $this->limitPage($limit);
    	for ($i = 1; $i <= $nbPage; $i++) {
    		$index[$i]['page'] = $i;
    		$index[$i]['display'] = $i;
    		$index[$i]['limit'] = $limit;
    		$index[$i]['offset'] = $limit * ($i - 1); 
    	}
    	if ($page < $nbPage) {
    		$index[$i + 1]['page'] = $page + 1;
    		$index[$i + 1]['display'] = "&gt;";
    		$index[$i + 1]['limit'] = $limit;
    		$index[$i + 1]['offset'] = $offset + $limit;
    	}
    	$this->data['nbUsers'] = $this->nbUsers;
    	$this->data['currentPage'] = ceil(($offset + 1) / $limit);
    	$this->data['index'] = $index;
    	$this->data['main'][] = 'profiles';
    	$this->load_ajax_template();
    }
}
