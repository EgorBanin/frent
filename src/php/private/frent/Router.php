<?php

namespace frent;

class Router {
	
	private $routes;
	
	public function __construct($routes) {
		$this->routes = $routes;
	}
	
	public function route($url, $actionDir) {
		$params = [];
		$pattern = arr_usearch($this->routes, function($pattern) use($url, &$params) {
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
			$actionFile = $actionDir.'/'.str_template($this->routes[$pattern], $params);
			
			if (is_readable($actionFile)) {
				$action = require $actionFile;
				
				return [$action, $params];
			}
		}
		
		return false;
	}
}
