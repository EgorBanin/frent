<?php

namespace frent;

return function($app, $params) {
	
	$login = arr_get($_POST, 'login', '');
	$password = arr_get($_POST, 'password', '');
	
	$sessionId = null;
	$errors = [];
	
	if (empty($login)) {
		$errors[] = 'Обязательно укажите логин';
	}
	
	if (empty($password)) {
		$errors[] = 'Обязательно укажите пароль';
	}
	
	if (empty($errors)) {
		$table = new \Mysql\Table($app->db, 'users');
		$user = $table->selectOne(['loginHash' => md5($login, true), 'active' => true]);

		if ($user) {
			if ($user['passwordHash'] !== hash('sha512', $login.$password, true)) {
				$errors['password'] = 'Неверный пароль';
			}
		} else {
			$errors['login'] = 'Пользователя с таким именем не существует';
		}
	}
	
	if (empty($errors)) {
		// login
		$sessionId = 1;
	}
	
	return json_encode([
		'sessionId' => $sessionId,
		'errors' => $errors,
	], JSON_UNESCAPED_UNICODE);
	
};
