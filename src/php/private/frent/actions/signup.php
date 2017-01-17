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
		$user = $table->selectOne(['loginHash' => md5($login, true)]);

		if ($user) {
			$errors['login'] = 'Пользователь с таким именем уже существует';
		}
	}
	
	if (empty($errors)) {
		$id = $table->insert([
			'login' => $login,
			'loginHash' => md5($login, true),
			'passwordHash' => hash('sha512', $login.$password, true),
			'active' => true,
		]);
		$app->auth->logout();
		$sessionId = $app->auth->login($id);
	}
	
	return json_encode([
		'sessionId' => $sessionId,
		'errors' => $errors,
	], JSON_UNESCAPED_UNICODE);
	
};
