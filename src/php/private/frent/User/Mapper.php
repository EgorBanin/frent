<?php

namespace frent\User;

class Mapper extends \frent\DbMapper {
	
	protected function table() {
		return $this->db->table('users');
	}
	
}

