<?php

require_once('user.inc.php');
/*
class HttpBasicAuth extends \Slim\Middleware {
	public function call() {
		//error_log($this->app->request()->getPathInfo());
		if (substr($this->app->request()->getPathInfo(), 0, 8) === '/noauth/') {
		//if(strpos($this->app->request()->getPathInfo(), '/noauth/') !== false) {
			$this->next->call();
			return;
		}
		$login = $this->app->request->headers('PHP_AUTH_USER');
		$password = $this->app->request->headers('PHP_AUTH_PW');
		$db = db_connect();
		$stmt = $db->prepare('select firstname, lastname from users where login=:login and password=:password');
		$stmt->bindParam(':login', $login, PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$stmt->execute();
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) === 0) {
			error_log('401');
			$this->app->response->setStatus(401);
		} else {
			$this->next->call();
		}
	}
}
*/


class TokenAuth extends \Slim\Middleware {
	public function call() {
		if ( (substr($this->app->request()->getPathInfo(), 0, 8) === '/noauth/')
		  || (substr($this->app->request()->getPathInfo(), 0, 6) === '/test/')
		  || (substr($this->app->request()->getPathInfo(), 0, 7) === '/login/'))
		{
			//error_log('SKIPPING TokenAuth: '.$this->app->request()->getPathInfo());
			//if(strpos($this->app->request()->getPathInfo(), '/noauth/') !== false) {
			$this->next->call();
			return;
		}
		//error_log(print_r($this->app->request->headers("Authorization"),1));

		$token = $this->app->request->headers->get('Authorization');

		$pk_user_id = (int)user_check_token_and_postpone($token);

		/*$db = db_connect();
		$stmt = $db->prepare('select user_check_token_and_postpone(:token)');
		$stmt->bindParam(':token', $token, PDO::PARAM_STR);
		$stmt->execute();
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//error_log(print_r($res,1));
		$pk_user_id = 0;
		if ((count($res) == 1) && (array_key_exists('user_check_token_and_postpone',$res[0]))) {
				$pk_user_id = $res[0]['user_check_token_and_postpone'];
		}*/
		if ($pk_user_id > 0) {
			$this->app->pk_user_id = $pk_user_id;
			$this->next->call();
		} else {
			//error_log(print_r($res[0],1));
			// serve_error cannot be used here because it throws 'Stop'
			// and throwing an exception from within the middleware
			// causes a 500-error
			//serve_error($this->app, 'invalid token', 401);
			error_log("401 for ".$token);
			serve_json(
					$this->app,
					get_error_message(
						'invalid token',
						$this->app->request->getMethod(),
						$this->app->request->getPath()
					),
					401);
		}
	}
}

