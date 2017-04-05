<?php

namespace frent;

return function($app, $params) {
	
	return ob_include(__DIR__.'/html.tpl.php');
	
};