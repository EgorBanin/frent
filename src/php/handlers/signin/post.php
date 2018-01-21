<?php

return function($rq, $rs, $globals) {

	$login = $rq->post('login');
	$password = $rq->post('password');
	$remember = (bool) $rq->post('remember', false);
	$sessionId = null;
	$errors = [];
	
	if (empty($login)) {
		$errors['login'] = 'Обязательно укажите логин';
	}
	
	if (empty($password)) {
		$errors['password'] = 'Обязательно укажите пароль';
	}
	
	if (empty($errors)) {
		$userStorage = new \users\Storage($globals->getDb());
		$user = $userStorage->getByLogin($login);
		if ($user && $user->active) {
			if ($user->passwordHash === $userStorage->passwordHash($password, $login)) {
				$auth = new \users\Auth($globals->getSession());
				$auth->logout();
				$sessionId = $auth->login($user->id, \users\Auth::fingerprint(), $remember? (60 * 60 * 24 * 7) : 0);
			} else {
				$errors['password'] = 'Неверный пароль';
			}
		} else {
			$errors['login'] = 'Пользователя с таким именем не существует';
		}
	}
	
	if (empty($errors)) {
		$userData = [
			'id' => $user->id,
			'name' => $user->login,
		];
	} else {
		$userData = null;
	}
	
	return $rs
		->setCode(empty($errors)? 200 : 403)
		->setBody(
			json_encode([
				'user' => $userData,
				'sessionId' => $sessionId,
				'errors' => $errors,
			], JSON_UNESCAPED_UNICODE)
		);

};
