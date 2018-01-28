<?php

return function(Request $rq, Response $rs, Registry $globals) {
	
	return $rs->setBody(Layout::main()->render([
		'title' => 'Вход',
		'content' => ob_include(__DIR__ . '/form.phtml'),
	]));
	
};
