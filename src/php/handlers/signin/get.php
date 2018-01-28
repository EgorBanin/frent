<?php

return function(Request $rq, Response $rs, Registry $globals) {
	
	return $rs->setBody(Layout::main()->render([
		'content' => ob_include(__DIR__ . '/form.phtml'),
	]));
	
};
