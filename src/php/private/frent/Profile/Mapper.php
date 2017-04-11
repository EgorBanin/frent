<?php

namespace frent\Profile;

class Mapper extends \frent\DbMapper {
	
	protected function table() {
		return $this->db->table('profiles');
	}
	
}
