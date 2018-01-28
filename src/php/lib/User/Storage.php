<?php

namespace User;

class Storage extends \Storage {

	public function __construct(\Mysql\Client $db) {
		parent::__construct($db, 'users');
	}

	public function getByLogin($login) {
		$row = $this->selectOne([
			'login' => $login,
			'loginHash' => $this->loginHash($login),
		]);
		
		return $this->map($row);
	}

	public function insert($fields) {
		$password = arr_take($fields, 'password');
		$id = $this->getTable()->insert(array_merge($fields, [
			'loginHash' => $this->loginHash($fields['login']),
			'passwordHash' => $this->passwordHash($password, $fields['login']),
		]));
		
		return $id;
	}

	public function loginHash($login) {
		return md5($login, true);
	}
	
	public function passwordHash($password, $salt) {
		return hash('sha512', $salt.$password, true);
	}

}