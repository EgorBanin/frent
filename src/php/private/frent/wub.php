<?php

namespace frent;


function arr_get($array, $key, $defaultValue = null) {
	return array_key_exists($key, $array)? $array[$key] : $defaultValue;
}

function arr_take(&$array, $key, $defaultValue = null) {
	$val = get($array, $key, $defaultValue);
	unset($array[$key]);
	
	return $val;
}

function arr_usearch($array, $func) {
	$result = false;
	foreach ($array as $k => $v) {
		if (call_user_func($func, $k, $v)) {
			$result = $k;
			break;
		}
	}
	
	return $result;
}

function http_get_current_request() {
	static $current = null;
	
	if ($current) {
		return $current;
	}

	if (isset($_SERVER['REQUEST_METHOD'])) {
		$method = $_SERVER['REQUEST_METHOD'];
	} else {
		trigger_error('Can\'t find request method', \E_USER_WARNING);
		$method = 'UNKNOWN';
	}

	if (isset($_SERVER['REQUEST_URI'])) {
		$url = $_SERVER['REQUEST_URI'];
	} else {
		trigger_error('Can\'t find request uri', \E_USER_WARNING);
		$url = '/';
	}

	$headers = http_get_request_headers();

	if ($headers === false) {
		trigger_error('Can\'t find request headers', \E_USER_WARNING);
		$headers = [];
	}

	$body = file_get_contents('php://input');

	return [
		'method' => $method,
		'url' => $url,
		'headers' => $headers,
		'body' => $body
	];
}

function http_get_request_headers() {
	if (function_exists('\getallheaders')) {
		return \getallheaders();
	} else {
		$headers = [];
		foreach ($_SERVER as $key => $value) {
			if (strpos($key, 'HTTP_') === 0) {
				$name = implode('-', array_map(function($v) {
					return ucfirst(strtolower($v));
				}, explode('_', substr($key, 5))));
				$headers[$name] = $value;
			}
		}
		
		return $headers;
	}
}

function http_send_request($code, $headers, $body) {
	if (\headers_sent()) {
		\trigger_error('Headers already sent', \E_USER_WARNING);
		echo $body;

		return false;
	} else {
		\http_response_code($code);

		foreach ($headers as $header) {
			\header($header, true);
		}

		echo $body;

		return true;
	}
}

function http_redirect($url, $code = 302) {
	return http_send_request($code, ['Location: '.$url], '');
}


function str_template($template, $vars) {
	$replaces = [];
	foreach ($vars as $name => $value) {
		$replaces["\{$name\}"] = $value;
	}

	return strtr($template, $replaces);
}

/**
 * Подключение файла с буферизацией вывода
 * @param string $file
 * @param array $params
 * @return sting
 */
function ob_include($file, array $params = []) {
	extract($params);
	ob_start();
	require func_get_arg(0);

	return ob_get_clean();	
}