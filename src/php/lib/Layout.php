<?php

class Layout {
	
	private $template;
	
	private $defaults;
	
	public function __construct($template, array $defaults) {
		$this->template = $template;
		$this->defaults = $defaults;
	}
	
	public static function main() {
		return new self(
			__DIR__ . '/../layouts/main.phtml',
			[
				'title' => 'Frent',
				'content' => '',
			]
		);
	}
	
	public function render($params) {
		return ob_include(
			$this->template,
			array_merge($this->defaults, $params)
		);
	}
	
}