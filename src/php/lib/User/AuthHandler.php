<?php

namespace User;

class AuthHandler {
	
	public function __invoke($pipe, $rq, $rs, $globals) {
		$auth = new Auth($globals->getSession());
		$id = $auth->auth(Auth::fingerprint());
		
		if ( ! $id) {
			throw new \Exception('Пользователь не авторизован');
		}
	}
	
}

