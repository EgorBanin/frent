<?php

class Router {
	
	private $routes;
	
	public function __construct($routes) {
		$this->routes = $routes;
	}
	
	public function route($url) {
		$params = [];
		$pattern = arr_usearch($this->routes, function($pattern) use($url, &$params) {
			$matches = [];
			if (preg_match($pattern, $url, $matches) === 1) {
				foreach ($matches as $name => $value) {
					if (is_string($name)) {
						$params[$name] = $value;
					}
				}
				return $pattern;
			}
		});
		
		if ($pattern !== false) {
			$handlerFile = str_template($this->routes[$pattern], $params);

			return [$handlerFile, $params];
		}
		
		return [null, []];
	}
}