<?php

return function($rq, $rs, $global) {
	
	return $rs->setBody(ob_include(__DIR__ . '/index.phtml', [
		'content' => 'Frent!',
	]));

};