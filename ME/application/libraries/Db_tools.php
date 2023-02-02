<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Db_tools {

	// renvoie un array indexé sur la PK
	public function reorg($pk, $rows) {
		$res = array();
		foreach ($rows as $row) {
			$res[ $row->$pk ] = $row;
		}
		return $res;
	}

	// renvoie un array d'arrays
	// le premier niveau est indexé sur k1 (par ex l'id de serveur)
	// le second niveau est indexé sur k2 (par ex l'id de disque du serveur)
	public function reorg_multi($k1, $k2, $rows) {
		$res = array();
		foreach ($rows as $row) {
			if (!isset($res[ $row->$k1 ]))
				$res[ $row->$k1 ] = array();
			$res[ $row->$k1 ][ $row->$k2 ] = $row;
		}
		return $res;
	}
}
