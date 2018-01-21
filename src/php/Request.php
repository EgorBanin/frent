<?php

class Request {
	
	private $httpRequest;

	private $params;

	public function __construct($httpRequest, $params) {
		$this->httpRequest = $httpRequest;
		$this->params = $params;
	}

	public function post($name, $default = null) {
		return io_post($name, $default);
	}

	public function get($name, $default = null) {
		return io_get($name, $default);
	}

	public function getCookie($name) {
		return $_COOKIE[$name]?? null;
	}

}