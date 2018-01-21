<?php

namespace frent;

function($app) {
	
	$user = $app->auth();
	
	$name = trim(io_post('name', ''));
	$summary = trim(io_post('summary', ''));
	$errors = [];
	
	if (empty($name)) {
		$errors['name'] = 'Обязательно укажите имя';
	}
	
	if (empty($summary)) {
		$errors['summary'] = 'Обязательно добавте описание';
	}
	
	if (empty($errors)) {
		$mapper = new Profile\Mapper($app->db);
		$id = $mapper->insert([
			'name' => $name,
			'summary' => $summary,
			'userId' => $user['id'],
			'ct' => time(),
		]);
	} else {
		$id = null;
	}
	
	return json_encode([
		'id' => $id,
		'errors' => $errors,
	], JSON_UNESCAPED_UNICODE);
};