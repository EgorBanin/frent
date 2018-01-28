<?php

namespace Session;

class Storage extends \Storage {

	public function __construct(\Mysql\Client $db) {
		parent::__construct($db, 'sessions');
	}

	public function map($row) {
		if ( ! $row) {
			return null;
		}

		return new Session(
			$row['id'],
			$row['token'],
			0, // todo
			json_decode($row['data']?? '[]', true)
		);
	}

	public function save(Session $session) {
		$row = obj_to_array($session);
		if ($row['id']) {
			$id = $row['id'];
			$this->set($id, [
				'token' => $row['token'],
				'data' => json_encode($row['data'], JSON_UNESCAPED_UNICODE),
				'ut' => time(),
			]);
		} else {
			$id = $this->insert([
				'token' => $row['token'],
				'description' => '',
				'data' => json_encode($row['data'], JSON_UNESCAPED_UNICODE),
				'deleted' => false,
				'ut' => time(),
			]);
		}

		return $id;
	}

}