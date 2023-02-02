<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Tools {
	public $CI = NULL;

	function __construct() {
		$this->CI = & get_instance();
	}
	function byte_convert($size, $sign='', $s='&nbsp;') {
		if (empty($size)) return '';
		if ($size<0) $sign='';
		# size smaller then 1KB
		if (abs($size) < 1024) return $sign.$size . $s . 'Bytes';
		# size smaller then 1MB
		if (abs($size) < 1048576) return $sign.round($size/1024,2). $s . 'KB';
		# size smaller then 1GB
		if (abs($size) < 1073741824) return $sign.round($size/1048576,2). $s . 'MB';
		# size smaller then 1TB
		if (abs($size) < 1099511627776) return $sign.round($size/1073741824,2). $s . 'GB';
		# size larger then 1TB
		else return $sign.round($size/(1024*1073741824),2). $s . 'TB';
	}

	function bits_convert($size, $s='&nbsp;') {
		# size smaller then 1Kb
		if (abs($size) < 1000) return $size . $s . 'bits';
		# size smaller then 1Kb
		if (abs($size) < 1000000) return round($size/1000,2). $s . 'Kb';
		# size smaller then 1Gb
		if (abs($size) < 1000000000) return round($size/1000000,2). $s . 'Mb';
		# size smaller then 1Tb
		if (abs($size) < 1000000000000) return round($size/1000000000,2). $s . 'Gb';
		# size larger then 1tb
		else return round($size/(1000000000000000),2). $s . 'Tb';
	}


	function bigint_format($n, $sign='', $s='&nbsp;'){
		if (empty($n)) return '';
		if ($n<0) $sign='';
		return $sign.number_format($n, 0, '.', $s );
	}

	function seconds_humanreadable($sec) {
		$m = intval($sec/60);
		$s = $sec % 60;
		$res = '';
		if ($m > 0) {
			$res = $this->minutes_humanreadable($m).' ';
		}
		return $res.$s.'s';
	}
	function minutes_humanreadable($min) {
		$res = '';
		if ($min < 0) {
			$res = $min;
		} else {
			$h = intval($min / 60);
			$min = $min % 60;
			$res = $min .'min';
			if ($h > 0) {
				$d = intval($h / 24);
				$h = $h % 24;
				$res = $h.'h '.$res;
				if ($d > 0)
					$res = $d.'j '.$res;
			}
		}
		return $res;
	}

	function exec_script($script, $params) {
		$cmd = './'.$script;
		foreach ($params as $p) {
			$cmd .= ' '.$p;
		}
		$cwd = getcwd();
		chdir(get_instance()->config->item('scripts_dir'));
		//echo getcwd();
 		//echo escapeshellcmd($cmd);
		exec(escapeshellcmd($cmd), $output, $return_var);
		chdir($cwd);
		//print_r($output);
		//print_r($return_var);
		return json_decode($output[0]);
	}

	function get_guid() {
		return bin2hex(openssl_random_pseudo_bytes(10));
	}

	function percent($n, $d, $precision=0) {
		if ($d == 0)
			return 'N/A';
		return round($n*100/$d, $precision).'%';
	}

	function date_us2fr($us) {
		$month = array('janv','févr','mars','avr','mai','juin','juil','août','sept','oct','nov','déc');
		$d = explode('/',$us);
		if (count($d)!=3) return '';
		return $d[2].' '.$month[(int)$d[1]-1].' '.$d[0];
	}

	function get_param($name, $default) {
		$query = $this->CI->db->query('SELECT value FROM parameter where name=?', array($name));
		if ($query->num_rows() != 1)
			return $default;
		$res = $query->result();
		return $res[0]->value;
	}

	function send_to_rabbit($queue, $obj) {
		$connection = new AMQPStreamConnection(TEMPR_MQ_HOST, 5672, 'lapin', 'lapin');
		$channel = $connection->channel();
		$channel->queue_declare($queue, false, false, false, false);

		$msg = new AMQPMessage(json_encode($obj));
		$channel->basic_publish($msg, '', $queue);
		$channel->close();
		$connection->close();
	}
	
}
