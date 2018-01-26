<?php

class Registry {

	private $app;
	
	private $db;

	private $session;

	public function __construct(App $app) {
		$this->app = $app;
	}

	public function getDb(): \Mysql\Client {
		if ($this->db === null) {
			$this->db = \Mysql\Client::init(
				$this->app->getConfigVal('mysql.username'),
				$this->app->getConfigVal('mysql.password')
			)
			->defaultDb($this->app->getConfigVal('mysql.db'))
			->charset($this->app->getConfigVal('mysql.charset'));
		}

		return $this->db;
	}

	public function getSession(): \session\Session {
		if ($this->session === null) {
			$sessionController = new \session\Controller(
				new \session\Storage($this->getDb())
			);
			$session = $sessionController->start($this->app->getRequest());
			$this->app->addAfterHandler(function($rq, $rs, $global) use($sessionController, $session) {
				$newRs = $sessionController->write($session, $rs);

				return $newRs;
			});
			$this->session = $session;
		}

		return $this->session;
	}

}