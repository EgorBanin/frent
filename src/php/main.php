<?php

require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/func_all.php';

$router = new Router([
	'~^get /$~' => 'index.php',
	'~^(?<method>get|post) /(?<module>[^/]+)$~' => '{module}/{method}.php',
]);
$httpRequest = (object) io_get_request();
list($handlerFile, $params) = $router->route(
	strtolower($httpRequest->method) . ' ' . $httpRequest->url
);
$rq = new Request($httpRequest, $params);
$configFile = getenv('PHP_USER_CONFIG')?: 'config.php';
$configPath = __DIR__ . '/' . $configFile;
$app = new App($configPath, $rq);

return $app(__DIR__ . '/handlers/' . $handlerFile);