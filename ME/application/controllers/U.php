<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// page de partage d'un profil
class U extends Page {

    public function __construct() {
        parent::__construct();
    }

    // see also routes.php for url routing
    public function index($login) {
        $this->data['css'][] = 'u.css';
        $pattern = '/^[[:alnum:]-_]+$/';
        $lang = @strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        if ($lang == "FR") {
            $strings = ["by" => "D'après ses amis"];
            $appstore = "https://www.tempr.me/images/BoutonAppStoreFR.png";
        } else {
            $strings = ["by" => "By their friends"];
            $appstore = "https://www.tempr.me/images/BoutonAppStoreEN.png";
        }
        $this->data['lang'] = $strings;
        $this->data['appstore'] = $appstore;

        if (preg_match($pattern,$login)) {
            $this->data['tracking'] = 'ct='.$login.'&';
            $this->s3tools->init();
            $sql = "select * from view_profiles where login=?";
            $query = $this->db->query($sql, array($login));
            $u = $query->result();

            if (count($u) !== 1) {
                redirect('/');
            }
            $u = $u[0];
            unset($u->password); // because you never know

            $other_user_id = $u->pk_user_id;

            // top3 hashtags
            $stmt = $this->db->conn_id->prepare("select tag, pop from view_user_recent_trends WHERE fk_user_id = :other_user_id order by pop desc LIMIT 3");
            $stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
            $stmt->execute();
            $obj = $stmt->fetchAll(PDO::FETCH_NUM);

            $this->s3tools->init();
            if ($u->filename_profile != NULL) {
                $u->url_profile = $this->s3tools->resolve_S3_filename($u->filename_profile);
            } else {
                $u->url_profile = "https://www.tempr.me/images/Empty3.png";
            }
            $u->url_background = $this->s3tools->resolve_S3_filename($u->filename_background);

            $this->data['u'] = $u;
            $this->data['obj'] = $obj;
            $this->data['main'][] = 'u';

            $title = $u->firstname.': ';
            foreach($obj as $o) {
                $title .= '#'. $o[0] .' ';
            }
            $description = 'My best hashtags revealed by TEMPR app. Only on iOS.';
            $this->data['tags'] = $this->prepare_og_data($title, $description, 'tempr', current_url(), $u->url_profile);

            $this->load_template();
        } else {
            redirect('/');
        }

    }
}
