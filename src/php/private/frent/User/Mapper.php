<?php

namespace frent\User;

class Mapper {
	
	protected $db;
	
	public function __construct($db) {
		$this->db = $db;
	}
	
	public function get($id) {
		$row = $this->db
			->table('users')
			->get($id);
		
		return $row;
	}
	
}

