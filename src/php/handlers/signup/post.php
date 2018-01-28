<?php

return function($rq, $rs, $globals) {

	$login = $rq->post('login', '');
	$password = $rq->post('password', '');
	$errors = [];
	$userId = null;
	$sessionId = null;

	if (empty($login)) {
		$errors[] = 'Обязательно укажите имя.';
	}

	if (empty($errors)) {
		$userStorage = new \User\Storage($globals->getDb());
		$anotherUser = $userStorage->getByLogin($login);
		if ($anotherUser) {
			$errors[] = 'Пользователь с таким именем уже зарегистрирован.';
		}
	}

	if (empty($errors)) {
		$userId = $userStorage->insert([
			'login' => $login,
			'password' => $password,
			'data' => '{}',
			'active' => true,
		]);
		$auth = new \User\Auth($globals->getSession());
		$auth->logout();
		$sessionId = $auth->login($userId, \User\Auth::fingerprint(), 0);
	}
	
	return $rs
		->setCode(empty($errors)? 201 : 403)
		->setBody(
			json_encode([
				'userId' => $userId,
				'sessionId' => $sessionId,
				'errors' => $errors,
			], JSON_UNESCAPED_UNICODE)
		);

};