<?php

namespace frent;

$appDir = realpath(__DIR__.'/../private');

set_include_path(
	get_include_path()
	.PATH_SEPARATOR.$appDir
);
require 'frent/autoload.php';

$configFile = getenv('PHP_USER_CONFIG')?: 'config.php';
$config = require 'frent/'.$configFile;

$app = new App($appDir.'/frent/actions', $appDir.'/frent/templates');
$app->db = \Mysql\Client::init($config['mysql']['username'], $config['mysql']['password'])
	->defaultDb($config['mysql']['db'])
	->charset($config['mysql']['charset']);
$app->auth = new Auth();

$routes = [
	'~^/$~' => 'index.php',
	'~^/login$~' => 'login.php',
	'~^/signup$~' => 'signup.php',
	'~/(?<slug>[^/]+$)~' => 'pages/view.php',
];
$currentRequest = http_get_current_request();
$url = parse_url($currentRequest['url'], PHP_URL_PATH);
$route = App::route($url, $routes);
if ($route) {
	if (
		isset($currentRequest['headers']['Accept'])
		&& strpos($currentRequest['headers']['Accept'], 'application/json') !== false
	) {
		list($action, $params) = $route;
		$app->run($action, $params);
	} else {
		// html
		$app->run('html.php', []);
	}
} else {
	// 404
}
