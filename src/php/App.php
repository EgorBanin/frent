<?php

class App {

	private $config;

	private $request;

	private $globals;

	private $afterHandlers = [];

	public function __construct(string $configPath, Request $request) {
		$this->config = new ConfigLoader($configPath);
		$this->request = $request;
		$this->globals = new Registry($this);
	}

	public function __invoke($handlerPath) {
		if (is_file($handlerPath) && is_readable($handlerPath)) {
			$handler = require $handlerPath;
			try {
				$rs = call_user_func($handler, $this->request, Response::ok(), $this->globals);
				$rs = $this->after($this->request, $rs, $this->globals);
			} catch(\Throwable $e) {
				throw $e; // DEBUG
				$rs = Response::error(500);
			}
		} else {
			$rs = Response::error(404);
		}

		return io_send_response($rs->getCode(), $rs->getHeaders(), $rs->getBody());
	}

	public function getConfigVal($name, $nameSeparator = '.') {
		return $this->config->get($name, $nameSeparator);
	}

	public function getRequest() {
		return $this->request;
	}

	public function addAfterHandler(callable $handler) {
		$this->afterHandlers[] = $handler;
	}


	private function after($rq, $rs, $globals) {
		foreach ($this->afterHandlers as $handler) {
			$rs = $handler($rq, $rs, $globals);
		}

		return $rs;
	}

}