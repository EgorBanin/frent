<?php


/**
 * Набор полезных PHP-функций
 * @link https://github.com/EgorBanin/wub
 */


/**
 * Получить значение из массива по ключу
 * @param array $array
 * @param mixed $key
 * @param mixed $defaultValue
 * @return mixed
 */
function arr_get($array, $key, $defaultValue = null) {
	return array_key_exists($key, $array)? $array[$key] : $defaultValue;
}

/**
 * Извлечь значение из массива по ключу
 * @param array $array
 * @param mixed $key
 * @param mixed $defaultValue
 * @return mixed
 */
function arr_take(&$array, $key, $defaultValue = null) {
	$val = arr_get($array, $key, $defaultValue);
	unset($array[$key]);
	
	return $val;
}

/**
 * Пользовательский поиск по массиву
 * @param array $array
 * @param callback $func
 * @return mixed ключ найденого значения или false, если значение не найдено
 */
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

/**
 * Обновить массив заменяя значения исходного массива
 * соответствующими значениями второго массива
 * @param array $array изменяется по ссылке
 * @param array $update
 */
function arr_update(&$array, $update) {
	array_walk($array, function(&$val, $key, $update) {
		if (array_key_exists($key, $update)) {
			$val = $update[$key];
		}
	}, $update);
}

/**
 * Получить массив только с указанными ключами
 * @param array $array
 * @param array $keys
 * @return array
 */
function arr_pick($array, $keys) {
	return array_intersect_key($array, array_flip($keys));
}

/**
 * Получить массив без указанных ключей
 * @param array $array
 * @param array $keys
 * @return array
 */
function arr_omit($array, $keys) {
	return array_diff_key($array, array_flip($keys));
}

/**
 * Проиндексировать список массивов по ключу
 * @param array $array массив ассосиативных массивов
 * @param string $key
 * @return array
 */
function arr_index($array, $key) {
	$index = [];
	foreach ($array as $v) {
		if (array_key_exists($key, $v)) {
			$index[$v[$key]] = $v;
		}
	}
	
	return $index;
}


/**
 * Получить комбинации значений массивов
 * @param array $arr1,... массивы, значения которых комбинируются
 * @return array
 */
function arr_comb(...$arrs) {
	$size = count($arrs);
	if ($size === 2) {
		list($a, $b) = $arrs;
		$combs = [];
		foreach ($a as $aVal) {
			foreach ($b as $bVal) {
				$combs[] = [$aVal, $bVal];
			}
		}

		return $combs;
	} elseif ($size > 2) {
		$last = array_pop($arrs);
		$combs = arr_comb(arr_comb(...$arrs), $last);

		return array_map(function($comb) {
			$flat = array_values($comb[0]); // разворачиваем только первый
			$flat[] = $comb[1];

			return $flat;
		}, $combs);
	} else {
		return $arrs;
	}
}

/**
 * Получить текущий запрос
 * Заголовки получаются с помощью io_get_request_headers.
 * Имена заголовков приводятся к виду Имя-Заголовка. Будте внимательны,
 * при использовании CamelCase или аббривиатур, буквы в верхнем регистре
 * в середине слова будут приведены к нижнему.
 * @return array {method, url, headers, body}
 */
function io_get_request() {
	static $current = null;

	if ($current) {
		return $current;
	}

	if (isset($_SERVER['SERVER_PROTOCOL'])) {
		$protocol = $_SERVER['SERVER_PROTOCOL'];
	} else {
		trigger_error('Can\'t find server protocol', \E_USER_WARNING);
		$protocol = 'UNKNOWN';
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

	$headers = io_get_request_headers();

	if ($headers === false) {
		trigger_error('Can\'t find request headers', \E_USER_WARNING);
		$headers = [];
	}

	$body = file_get_contents('php://input');

	return [
		0 => $protocol,
		'protocol' => $protocol,
		1 => $method,
		'method' => $method,
		2 => $url,
		'url' => $url,
		3 => $headers,
		'headers' => $headers,
		4 => $body,
		'body' => $body,
	];
}

/**
 * Получить заголовки текущего запроса
 * Имена заголовков приводятся к виду Имя-Заголовка. Будте внимательны,
 * при использовании CamelCase или аббривиатур, буквы в верхнем регистре
 * в середине слова будут приведены к нижнему.
 * @return array
 */
function io_get_request_headers() {
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

/**
 * Отправить ответ
 * @param int $code
 * @param array $headers массив или ассоциативный массив заголовков
 * @param string $body
 * @return boolean
 */
function io_send_response($code, $headers, $body) {
	if (\headers_sent()) {
		\trigger_error('Headers already sent', \E_USER_WARNING);
		echo $body;

		return false;
	} else {
		\http_response_code($code);

		foreach ($headers as $name => $value) {
			if (is_string($name)) {
				$header = $name.': '.$value;
			} else {
				$header = $value;
			}

			\header($header, true);
		}

		echo $body;

		return true;
	}
}

/**
 * Получить опции командной строки
 * @param array $args аргументы командной строки. Если не переданы, используется $argv.
 * @global array $argv
 * @return array
 */
function io_opt($args = null) {
	if ($args === null) {
		global $argv;
		$args = $argv;
		array_shift($args);
	}

	$opt = [];
	foreach ($args as $v) {
		$kv = explode('=', $v);
		if (count($kv) === 2) {
			list($name, $value) = $kv;
			$value = trim($value);
		} else {
			$name = $v;
			$value = true;
		}
		
		$name = ltrim(trim($name), '-');
		$opt[$name] = $value;
	}
	
	return $opt;
}

/**
 * Заменить вхождения строки '{varName}' на соответствующее значение из массива
 * @param string $template
 * @param array $vars
 * @return string
 */
function str_template($template, $vars) {
	$replaces = [];
	foreach ($vars as $name => $value) {
		$replaces['{'.$name.'}'] = $value;
	}

	return strtr($template, $replaces);
}

/**
 * Включение очень простой автозагрузки классов
 * Если переданы базовые директории, они будут добавлены в include path
 * @param string ...$baseDir
 */
function enable_class_autoloading() {
	$baseDirs = func_get_args();
	if ($baseDirs) {
		$include = '';
		foreach ($baseDirs as $baseDir) {
			$include .= PATH_SEPARATOR.$baseDir;
		}
		set_include_path(get_include_path().$include);
	}

	spl_autoload_register(function($className) {
		$fileName = stream_resolve_include_path(
			strtr(ltrim($className, '\\'), '\\', '/').'.php'
		);

		if ($fileName) {
			require_once $fileName;
		}
	});
}

/**
 * Подключение файла с буферизацией вывода
 * @param string $file
 * @param array $params
 * @return string
 */
function ob_include($file, array $params = []) {
	extract($params);
	ob_start();
	require func_get_arg(0);

	return ob_get_clean();	
}


/**
 * Выполнить HTTP-запрос
 * Для работы функции необходима включённая директива allow_url_fopen.
 * @param string $method
 * @param string $url
 * @param array $headers массив строк-заголовков
 * @param string $body
 * @param array $options опции контекста
 * @throws \Exception
 */
function http_request($method, $url, array $headers, $body, array $options = []) {
	$options['http'] = [
		'method' => $method,
		'header' => $headers,
		'content' => $body,
		'max_redirects' => 0,
		'ignore_errors' => 1,
	];
	$context = stream_context_create($options);
	$stream = @fopen($url, 'r', false, $context);
	if ($stream === false) {
		throw new \Exception('Не удалось выполнить запрос '.$method.' '.$url);
	}
	$meta = stream_get_meta_data($stream);
	$responseHeaders = isset($meta['wrapper_data'])? $meta['wrapper_data'] : [];
	$responseBody = stream_get_contents($stream);
	fclose($stream);
	$responseStatus = [];
	if (preg_match(
		'/^(?<protocol>https?\/[0-9\.]+)\s+(?<code>\d+)\s+(?<comment>\S.*)$/i',
		reset($responseHeaders),
		$responseStatus
	) !== 1) {
		throw new \Exception('Не удалось распарсить статус ответа');
	}
	
	return [
		'code' => $responseStatus['code'],
		'headers' => $responseHeaders,
		'body' => $responseBody,
	];
}



function file_empty_dir($dir, $filter = null) {
	if ( ! $filter) {
		$filter = function($file) {
			return ($file !== '.' && $file !== '..');
		};
	}

	$files = scandir($dir);
	$result = true;
	foreach ($files as $name) {
		if ( ! $filter($name)) {
			continue;
		}

		$file = $dir.'/'.$name;
		if (is_dir($file)) {
			$result =
				$result
				&& file_empty_dir($file, $filter) // ! рекурсия
				&& @rmdir($file);
		} else {
			$result = $result && @unlink($file);
		}
	}

	return $result;
}