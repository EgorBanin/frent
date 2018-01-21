<?php

namespace Session;

class Session {

	const KEY_NAME = 'sessionKey';

	const KEY_SEPARATOR = ':';

	private $id;

	private $token;

	private $lifetime;
	
	private $data = [];

	private $ut;

	private $changed = false;

	public function __construct($id, $token, $lifetime, $data) {
		$this->id = $id;
		$this->token = $token;
		$this->lifetime = $lifetime;
		$this->data = $data;
	}

	public function setLifetime($lifetime) {
		$this->lifetime = $lifetime;
		$this->changed = true;
	}

	public function getKey() {
		return $this->id . self::KEY_SEPARATOR . bin2hex($this->token);
	}

	public function getExpire() {
		return $this->ut + $this->lifetime;
	}
	
	public function isNew() {
		return ($this->id === null);
	}
	
	public function isChanged() {
		return $this->changed;
	}

	public function __set($name, $val) {
		$this->data[$name] = $val;
		$this->changed = true;
	}

	public function __unset($name) {
		unset($this->data[$name]);
		$this->changed = true;
	}

	public function __get($name) {
		return $this->data[$name]?? null;
	}

	public static function parseKey($key) {
		$arr = explode(self::KEY_SEPARATOR, $key);
		if (count($arr) === 2) {
			$arr[1] = hex2bin($arr[1]);

			return $arr;
		} else {
			return [null, self::generateToken()];
		}
	}

	public static function generateToken() {
		return random_bytes(20);
	}
	
}
