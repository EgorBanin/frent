<?php

namespace frent;

abstract class DbMapper {
	
	protected $db;
	
	abstract protected function table();
	
	public function __construct(\Mysql\Client $db) {
		$this->db = $db;
	}
	
	public function get($id) {
		$row = $this->tbale()->get($id);
		
		return $row;
	}
	
	public function insert($fileds) {
		$id = $this->table()->insert($fileds);
		
		return $id;
	}
	
}
