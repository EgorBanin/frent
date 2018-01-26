<?php

class Response {

	private $code;

	private $headers;

	private $body;

	public function __construct(int $code, array $headers, string $body) {
		$this->code = $code;
		$this->headers = $headers;
		$this->body = $body;
	}

	public static function ok(array $headers = [], string $body = ''): self {
		return new self(200, [], '');
	}

	public static function error(int $code = 500, array $headers = [], string $body = ''): self {
		return new self(200, [], '');
	}

	public function getCode(): int {
		return $this->code;
	}

	public function setCode(int $code): self {
		$this->code = $code;

		return $this;
	}

	public function getHeaders(): array {
		return $this->headers;
	}

	public function addHeader($header): self {
		$this->headers[] = $header;

		return $this;
	}

	public function getBody(): string {
		return $this->body;
	}

	public function setBody(string $body): self {
		$this->body = $body;

		return $this;
	}

	public function setCookie(string $name, $val, $expire) {
		setcookie($name, $val);
	}

}