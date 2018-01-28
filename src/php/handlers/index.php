<?php

return function($rq, $rs, $global) {
	
	return $rs->setBody(Layout::main()->render([
		'content' => 'Привет!',
	]));

};