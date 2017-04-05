<?php

namespace frent;

$appDir = realpath(__DIR__.'/../private');
require_once $appDir.'/frent/wub.php'; // Beyond Lies the Wub
require_once $appDir.'/vendor/autoload.php';

$configFile = getenv('PHP_USER_CONFIG')?: 'config.php';
$config = require $appDir.'/frent/'.$configFile;

$app = new App(
	$appDir.'/frent/actions',
	new Router([
		'~^/$~' => 'index.php',
		'~^/login$~' => 'login.php',
		'~^/signup$~' => 'signup.php',
		'~/(?<slug>[^/]+$)~' => 'pages/view.php',
	])
);
$app->db = \Mysql\Client::init($config['mysql']['username'], $config['mysql']['password'])
	->defaultDb($config['mysql']['db'])
	->charset($config['mysql']['charset']);

$currentRequest = io_get_request();
$url = parse_url($currentRequest['url'], PHP_URL_PATH);
$route = $app->route($url);
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
