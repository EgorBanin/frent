<?php

return function(Request $rq, Response $rs, Registry $globals) {
	
	$auth = new \User\Auth($globals->getSession());
	$auth->logout();
	
	return Response::redirect('/', 303);
	
};