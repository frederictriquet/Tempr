<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LinkCities extends Page {
    /*
     * config of default settings
    */
    public function index() {
        $this->_displayLinkCities(false);
    }

    public function edit() {
        $this->_displayLinkCities(true);
    }

    private function _displayLinkCities($is_editing) {
        $devcities = $this->db->query('SELECT * FROM devcities WHERE fk_city_id IS NULL');
        $this->data['devcities'] = $devcities->result();

        $this->data['main'][] = 'linkcities';

        $this->load_template();
    }

    // AJAX REQUESTS

    public function ajax_update($devcityid, $cityid) {
        $linkcities = $this->db->conn_id->prepare("SELECT * FROM link_cities(:devcityid, :cityid)");
        $linkcities->bindParam(':devcityid', $devcityid, PDO::PARAM_INT);
        $linkcities->bindParam(':cityid', $cityid, PDO::PARAM_INT);
        $linkcities->execute();

        echo 'OK';
    }
    public function ajax_cities($devcity, $country_code) {
        $cities = $this->db->conn_id->prepare("SELECT pk_city_id, name, country, latitude, longitude FROM cities WHERE country = :country AND name ILIKE :devcity ORDER BY country ASC, name ASC");
        $cities->bindValue(":devcity", substr($devcity,0,3) . '%');
        $cities->bindValue(":country", $country_code);
        $cities->execute();

        header('application/json');
        echo json_encode($cities->fetchAll());
    }
}