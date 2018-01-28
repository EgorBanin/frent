<?php

namespace User;

class Auth {
	
	private $session;
	
	public function __construct(\Session\Session $session) {
		$this->session = $session;
	}
	
	public function login($id, $fingerprint, $lifetime) {
		$this->session->setLifetime($lifetime);
		$this->session->auth = [
			'id' => $id,
			'fingerprint' => $fingerprint,
		];

		return $this->session->getKey();
	}
	
	public function auth($fingerprint) {
		$data = $this->session->auth;
		if ( ! $data) {
			return null;
		}

		if ($data['fingerprint'] !== $fingerprint) {
			return null;
		}

		return $data['id'];
	}
	
	public function logout() {
		unset($this->session->auth);
	}

	public static function fingerprint() { // todo: в реквест
		$fingerprint = [
			isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : null,
			isset($_SERVER['HTTP_X_FORWARDED_FOR'])? $_SERVER['HTTP_X_FORWARDED_FOR'] : null,
			isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT'] : null,
		];
		
		return md5(json_encode($fingerprint));
	}
	
}