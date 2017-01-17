<?php

namespace frent;

class App {
	
	public $db;

	private $actionDir;
	
	private $templateDir;

	public function __construct($actionDir, $templateDir) {
		$this->actionDir = $actionDir;
		$this->templateDir = $templateDir;
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
		
		http_send_request($code, $headers, $body);
	}
	
	public function error($message) {
		echo $message;
		
		exit(1);
	}
	
	public function template($template, $params = []) {
		$templateFile = $this->templateDir.'/'.$template;
		
		return ob_include($templateFile, $params);
	}
	
	public static function route($url, $routes) {
		$params = [];
		$pattern = arr_usearch($routes, function($pattern) use($url, &$params) {
			$matches = [];
			if (preg_match($pattern, $url, $matches) === 1) {
				foreach ($matches as $name => $value) {
					if ( ! ctype_digit((string) $name)) {
						$params[$name] = $value;
					}
				}

				return $pattern;
			}
		});
		
		if ($pattern !== false) {
			$action = str_template($routes[$pattern], $params);
			
			return [$action, $params];
		} else {
			return false;
		}
	}
	
}
