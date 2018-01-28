<?php

namespace User;

class AuthMiddleware {

	private $handler;

	public function __construct(callable $handler) {
		$this->handler = $handler;
	}
	
	public function __invoke(\Request $rq, \Response $rs, \Registry $globals): \Response {
		$auth = new Auth($globals->getSession());
		$userId = $auth->auth(Auth::fingerprint());

		if ( ! $userId) {
			$rs->setCode(403);

			return $rs;
		}

		$userStorage = new Storage($globals->getDb());
		$user = $userStorage->get($userId);

		if ( ! $user) {
			$rs->setCode(403);

			return $rs;
		}

		return call_user_func($this->handler, $rq, $rs, $globals, $user);
	}

}