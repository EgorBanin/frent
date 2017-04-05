<?php

namespace frent;

class App {
	
	public $db;
	
	private $auth;

	private $actionDir;
	
	private $router;

	public function __construct($actionDir, Router $router) {
		$this->actionDir = $actionDir;
		$this->router = $router;
		$this->auth = new Auth();
	}
	
	public function run($action, $params) {
		try {
			$response = call_user_func($action, $this, $params);
		} catch (Exception $e) {
			$this->error('Непредвиденная ошибка: '.$e->getMessage());
		}
		
		if (is_string($response)) {
			$code = 200;
			$headers = [];
			$body = $response;
		} else {
			list($code, $headers, $body) = $response;
		}
		
		io_send_response($code, $headers, $body);
	}
	
	public function error($message) { // todo
		echo $message;
		
		exit(1);
	}
	
	public function route($url) {
		return $this->router->route($url, $this->actionDir);
	}
	
	public function login($userId) {
		return $this->auth->login($userId);
	}
	
	public function logout() {
		$this->auth->logout();
	}
	
	public function auth() {
		$userId = $this->auth->auth();
		if ($userId) {
			$mapper = new User\Mapper($this->db);
			$user = $mapper->get($id);
		} else {
			$user = false;
		}
		
		if ( ! $user) {
			$this->error('401 Требуется авторизация');
		}
		
		return $user;
	}
	
}
