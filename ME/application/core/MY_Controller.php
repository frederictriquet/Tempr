<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// extending CI_Controller multiple times, see:
// http://stackoverflow.com/questions/8004385/codeigniter-my-controller-is-it-only-possible-to-extend-core-once

class MY_Controller extends CI_Controller {
    public function __construct() {
        //error_log('Loading MY_Controller Class');
        parent::__construct();
    }
}

class Page extends MY_Controller {

    public $data = array();

    public function __construct() {
        //error_log('Loading Page Class');
        parent::__construct();
        $this->initialize();
    }

    public function initialize() {
        $this->data['css'][] = 'reset.css';
        //$this->data['css'][] = 'generic.css';
        //$this->data['css'][] = 'jquery.dataTables.css';
        //$this->data['css'][] = 'datatables.css';
        //$this->data['css'][] = 'jquery.toast.min.css';
        //$this->data['css'][] = 'waiting.css';
        //$this->data['css'][] = 'menu.css';
        //$this->data['css'][] = 'font-awesome.min.css';
        //$this->data['css'][] = 'popup.css';
        //	$this->data['css'][] = 'debug.css';

        //$this->data['css'][] = 'bootstrap.min.css';
        //$this->data['css'][] = 'bootstrap-theme.min.css';
        //$this->data['css'][] = 'my.css';

        //$this->data['js'][] = 'jquery-2.1.0.min.js';
        //$this->data['js'][] = 'jquery.dataTables.min.js';
        //$this->data['js'][] = 'jquery.toast.min.js';
        //$this->data['js'][] = 'jquery.waiting.min.js';
        //$this->data['js'][] = 'jquery.popup.min.js';

        //$this->data['js'][] = 'bootstrap.min.js';

        //$this->data['js'][] = 'last.js';

        // Title, Metadescription et Metakeywords par défaut :
        $this->data['title_default'] = $this->config->item('site_name');
        $this->data['title'] = $this->data['title_default'].'';
        $this->data['meta_description'] = '';
        $this->data['meta_keywords'] = '';
        // Contenu
        $this->data['top'] = array();
        $this->data['main'] = array();
        $this->data['bottom'] = array();

        //$this->data['ariane'] = array();
        //$this->data['ariane'][0] = new stdClass();
        //$this->data['ariane'][0]->label = 'Accueil';
        //$this->data['ariane'][0]->link = site_url();

    }

    protected function prepare_og_data($title, $description, $keywords, $url, $image) {
        $res = [
        ['meta','name','description', 'content',$description],
        ['meta','name','keywords', 'content',$keywords],
        ['meta','property','article:author', 'content','https://www.facebook.com/TemprApp'],
        ['meta','name','application-name', 'content','Tempr'],
        ['meta','itemprop','name','content',$title],
        ['meta','itemprop','description','content', $description],
        ['meta','property','fb:app_id','content','1531540413809812'],
        ['meta','property','og:title','content',$title],
        ['meta','property','og:type','content','article'],
        ['meta','property','og:site_name','content','Tempr'],
        ['meta','property','og:url','content',$url],
        ['meta','property','og:description','content',$description],
        ['meta','name','twitter:site','content','@Tempr_app'],
        ['meta','name','twitter:title','content',$title],
        ['meta','name','twitter:description','content',$description],
        ['meta','name','description', 'content',$description],
        ['link','rel','canonical','href',$url]
        ];
        if (!empty($image)) {
            $res[] = ['meta','itemprop','image','content', $image];
            $res[] = ['meta','property','og:image','content',$image];
            $res[] = ['meta','name','twitter:card','content','summary_large_image'];
            $res[] = ['meta','name','twitter:image','content',$image];
        }
        return $res;
    }

    protected function page_post($id_post, $filename_profile, $table='view_decorated_posts') {
        $lang = @strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        if ($lang == "FR") {
            $strings = ["you" => "TOI", "by" => "d'après", "join" => "Rejoins-nous sur Tempr !"];
            $appstore = "https://www.tempr.me/images/BoutonAppStoreFR.png";
        } else {
            $strings = ["you" => "YOU", "by" => "by", "join" => "Join us on Tempr !"];
            $appstore = "https://www.tempr.me/images/BoutonAppStoreEN.png";
        }
        $this->data['lang'] = $strings;
        $this->data['appstore'] = $appstore;

        // infos du post
        $posts = $this->db->conn_id->prepare("select * from ".$table." WHERE pk_post_id = :id_post");
        $posts->bindParam(':id_post', $id_post, PDO::PARAM_INT);
        $posts->execute();
        if ($posts->rowCount() == 1) {
            $posts = $posts->fetchAll(PDO::FETCH_ASSOC);
            $posts = $posts[0];

            $this->s3tools->init();
            if ($posts[$filename_profile] != NULL) {
                $posts['url_profile'] = $this->s3tools->resolve_S3_filename($posts[$filename_profile]);
            }
            if ($posts['filename'] != NULL) {
                $url = $posts['url_media'] = $this->s3tools->resolve_S3_filename($posts['filename']);
            } else {
                $url = NULL;
            }
            $this->data['posts'] = $posts;
            $title = $posts['to_firstname'].': ';
            if (!empty($posts['tag1'])) $title .= '#'. $posts['tag1'].' ';
            if (!empty($posts['tag2'])) $title .= '#'. $posts['tag2'].' ';
            if (!empty($posts['tag3'])) $title .= '#'. $posts['tag3'].' ';
            $description = 'The last hashtags of '.$posts['to_firstname'].', revealed by TEMPR app. Only on iOS.';
            $this->data['tags'] = $this->prepare_og_data($title, $description, 'tempr', current_url(), $url);

            $author = $this->db->conn_id->prepare("select login from users u join ".$table." on from_fk_user_id = u.pk_user_id WHERE pk_post_id = :id_post");
            $author->bindParam(':id_post', $id_post, PDO::PARAM_INT);
            $author->execute();
            if ($author->rowCount() == 1) {
                $author = $author->fetchAll(PDO::FETCH_NUM);
                $this->data['tracking'] = 'ct='.$author[0][0].'&';
            } else {
                $this->data['tracking'] = '';
            }
        } else {
            redirect('/');
        }
    }

    public function load_template() {

        //$this->data['top'][] = 'sub/menu';

        $template = '';

        $template.= $this->load->view('template/header', $this->data, TRUE);
        $template.= $this->load->view('template/main', $this->data, TRUE);
        $template.= $this->load->view('template/footer', $this->data, TRUE);

        if (isset($this->data['debug']))
            $template.= $this->load->view('debug', $this->data, TRUE);

        echo $template;
    }

    public function load_ajax_template() {
        $template = '';

        $template.= $this->load->view('template/header', $this->data, TRUE);
        $template.= $this->load->view('template/main', $this->data, TRUE);
        $template.= $this->load->view('template/footer', $this->data, TRUE);

        echo $template;
    }
}


class AjaxPage extends MY_Controller {

    public $data = array();

    public function __construct() {
        error_log('Loading AjaxPage Class');
        parent::__construct();
        $this->initialize();
    }

    public function initialize() {
        error_log('AjaxPage::initialize');
    }

    public function serve_data() {
        header('Content-Type: application/json');
        //$this->load->view('template/ajax', $this->data, FALSE);

        //if (isset($this->data['debug']))
        //	$template.= $this->load->view('debug', $this->data, TRUE);

        echo json_encode($this->data['data']);
    }
}
