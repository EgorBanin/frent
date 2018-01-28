<?php

return function(Request $rq, Response $rs, Registry $globals) {
	
	$auth = new \User\Auth($globals->getSession());
	$userId = $auth->auth(\User\Auth::fingerprint());
	
	return $rs->setBody(Layout::main()->render([
		'content' => 'Привет! ' . ($userId?? 'x'),
	]));

};