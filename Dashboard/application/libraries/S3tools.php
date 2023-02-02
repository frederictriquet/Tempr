<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class S3tools {
	public $CI = NULL;
	private $S3 = NULL;

	function __construct() {
		$this->CI = & get_instance();
	}

	function init() {
		if ($this->S3 === NULL) {
			$S3conf = array(
				'credentials' => array(
					'key'    => S3KEY,
					'secret' => S3SECRET
				),
				'region' => S3REGION,
				'version' => 'latest'
			);
			$this->S3 = Aws\S3\S3Client::factory($S3conf);
		}
		return $this->S3;
	}

	function resolve_S3_filename($filename) {
		$res = null;
		if (!is_null($filename)) {
			$cmd = $this->S3->getCommand('GetObject', [
					'Bucket' => S3BUCKET,
					'Key'    => $filename
					]);
			$request = $this->S3->createPresignedRequest($cmd, '+2 minutes');
			$res = (string) $request->getUri();
		}
		return $res;
	}
}
