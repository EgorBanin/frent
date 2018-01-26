<?php

class ConfigLoader {

	private $path;

	private $config;
	
	public function __construct($path) {
		$this->path = $path;
	}

	public function get($name, $nameSeparator = '.') {
		$config = $this->getConfig();
		$val = $config;
		$keys = explode($nameSeparator, $name);
		foreach ($keys as $key) {
			if(array_key_exists($key, $val)) {
				$val = $val[$key];
			} else {
				$val = null;
				break;
			}
		}

		return $val;
	}

	private function getConfig() {
		if ($this->config === null) {
			$this->config = $this->load($this->path);
		}

		return $this->config;
	}

	private function load($path) {
		$config = require($path);

		return $config;
	}

}