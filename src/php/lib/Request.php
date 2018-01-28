<?php

class Request {
	
	private $httpRequest;

	private $params;
	
	private $ip;
	
	private $post;
	
	private $get;

	public function __construct($httpRequest, $params, $ip) {
		$this->httpRequest = $httpRequest;
		$this->params = $params;
		$this->ip = $ip;
	}
	
	public function getIp() {
		return $this->ip;
	}
	
	public function getHeader($name, $default = null) {
		return $this->httpRequest->headers[$name]?? $default;
	}

	public function post($name, $default = null) {
		if ($this->post === null) {
			parse_str($this->httpRequest->body, $this->post);
		}
		
		return $this->post[$name]?? $default;
	}

	public function get($name, $default = null) {
		if ($this->get === null) {
			$query = parse_url($this->httpRequest->url, PHP_URL_QUERY);
			parse_str($query, $this->get);
		}
		
		return $this->get[$name]?? $default;
	}

	public function getCookie($name) {
		return $_COOKIE[$name]?? null;
	}

}