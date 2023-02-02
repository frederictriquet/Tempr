<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Page {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->data['css'][] = 'p.css';
        $lang = @strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        if ($lang == "FR") {
            $strings = ["by" => "D'après", 'team' => 'La Team Tempr'];
            $appstore = "https://www.tempr.me/images/BoutonAppStoreFR.png";
        }
        else {
            $strings = ["by" => "By", 'team' => 'The Tempr Team'];
            $appstore = "https://www.tempr.me/images/BoutonAppStoreEN.png";
        }
        $this->data['lang'] = $strings;
        $this->data['appstore'] = $appstore;

        // top3 hashtags
        $stmt = $this->db->conn_id->prepare("select tag, pop from view_user_recent_trends WHERE fk_user_id = :other_user_id order by pop desc LIMIT 3");
        $stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
        $stmt->execute();
        $p = ['from_firstname' => $strings['team'],
              'from_lastname' => '',
              'to_lastname' => 'Tempr',
              'to_firstname' => 'App',
              'url_profile' => "https://www.tempr.me/images/PhotoProfTempr.png",
              'url_media' => "https://www.tempr.me/images/PhotoPost.png",
              'tag1' => 'HashtagYourFriends',
              'tag2' => 'TheAppForYou',
              'tag3' => 'HaveFunWithTempr',
              'pop1' => 156,
              'pop2' => 77,
              'pop3' => 56
              ]
        ;

        $this->data['tracking'] = '';
        $this->data['posts'] = $p;
        $this->data['main'][] = 'p';
        
		$title = 'Tempr App: #'. $p['tag1'] .' #'. $p['tag2'] .' #'. $p['tag3'];
		$description = 'My best hashtags, revealed by TEMPR app. Only on iOS.';
		$this->data['tags'] = $this->prepare_og_data($title, $description, 'tempr', current_url(), $p['url_profile']);

        $this->load_template();

    }
}
