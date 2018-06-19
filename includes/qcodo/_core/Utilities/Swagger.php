<?php

namespace Qcodo\Utilities;
use QBaseClass;
use Exception;

class Swagger extends QBaseClass {
	protected $swaggerObject;
	protected $path;

	public function __construct($path) {
		if (!is_file($path)) throw new Exception('Swagger file not found: ' . $path);

		$this->swaggerObject = json_decode(file_get_contents($path));
		if (!$this->swaggerObject) throw new Exception('Invalid Swagger format: ' . $path);

		$this->path = $path;
	}
}