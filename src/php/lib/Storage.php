<?php

class Storage {
	
	private $db;

	private $tableName;

	private $primaryKey;
	
	public function __construct(\Mysql\Client $db, $tableName, $primaryKey = 'id') {
		$this->db = $db;
		$this->tableName = $tableName;
		$this->primaryKey = $primaryKey;
	}

	protected function getDb() {
		return $this->db;
	}

	protected function getTable() {
		return $this->db->table($this->tableName, $this->primaryKey);
	}

	public function map($row) {
		if ( ! $row) {
			return null;
		}

		return (object) $row;
	}
	
	public function get($id) {
		$row = $this->getTable()->get($id);
		
		return $this->map($row);
	}
	
	public function set($id, $fields) {
		return $this->getTable()->set($id, $fields);
	}
	
	public function rm($id) {
		return $this->getTable()->rm($id);
	}
	
	public function insert($fields) {
		return $this->getTable()->insert($fields);
	}
	
	public function select($where = []) {
		$rows = $this->getTable()->select($where);
		
		return array_map([$this, 'map'], $rows);
	}

	public function selectOne($where = []) {
		$row = $this->getTable()->selectOne($where);
		
		return $this->map($row);
	}
	
}