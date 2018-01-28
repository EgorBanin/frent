<?php

namespace Session;

class Controller {
	
	private $storage;

	public function __construct(Storage $storage) {
		$this->storage = $storage;
	}

	public function start($rq): Session {
		$sessionKey = $rq->getCookie(Session::KEY_NAME);
		if ($sessionKey) {
			list($id, $token) = Session::parseKey($sessionKey);
		} else {
			$id = null;
			$token = Session::generateToken();
		}
		
		if ($id) {
			$session = $this->storage->selectOne(['id' => $id, 'token' => $token]);
			if ( ! $session) {
				$session = new Session($id, $token, 0, []);
			}
		} else {
			$session = new Session($id, $token, 0, []);
		}
		
		if ($session->isNew()) {
			$id = $this->storage->save($session);
			obj_init($session, ['id' => $id]);
		}

		return $session;
	}

	public function write(Session $session, \Response $rs): \Response {
		if ($session->isChanged()) {
			$this->storage->save($session);
		}
		
		$rs->setCookie(Session::KEY_NAME, $session->getKey(), $session->getExpire());

		return $rs;
	}

}