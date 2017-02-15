<?php

namespace frent;

class App {
	
	public $db;
	
	public $auth;

	private $actionDir;
	
	private $templateDir;
	
	private $router;

	public function __construct($actionDir, $templateDir, Router $router) {
		$this->actionDir = $actionDir;
		$this->templateDir = $templateDir;
		$this->router = $router;
	}
	
	public function run($action, $params) {
		$actionFile = $this->actionDir.'/'.$action;
		if ( ! is_readable($actionFile)) {
			$this->error('Не удалось подключить файл '.$actionFile);
		}
		
		$func = require($actionFile);
		if ( ! is_callable($func)) {
			$this->error('Неверный результат подключения '.$actionFile);
		}
		
		try {
			$response = call_user_func($func, $this, $params);
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
	
	public function error($message) {
		echo $message;
		
		exit(1);
	}
	
	public function template($template, $params = []) {
		$templateFile = $this->templateDir.'/'.$template;
		
		return ob_include($templateFile, $params);
	}
	
	public function route($url) {
		return $this->router->route($url, $this->actionDir);
	}
	
}
