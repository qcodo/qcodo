<?php

namespace Qcodo\Utilities;
use QBaseClass;
use Exception;

class HttpRequest extends QBaseClass {
	public $method;
	public $path;

	public $pathParametersArray;
	public $queryStringParametersArray;
	public $postParametersArray;
	public $fileParametersArray;
	public $rawPost;
	public $headersArray;

	/**
	 * Given a requestPath and a namedPath, this will parse out path-based parameters (if any)
	 * and place into the array by order of parameter.
	 *
	 * So for example, given a namedPath of /foo/bar/{id}/{blah} and a requestPath of /foo/bar/15/20, this
	 * will return an array of [15, 20]
	 *
	 * This *does* assume that the paths are the same structure, and will throw if they have different number
	 * of path arguments.
	 *
	 * However, this does **NOT** perform any checking to confirm that the paths are a match.  That is the
	 * the responsibility of the caller.
	 *
	 * @param string $requestPath
	 * @param string $namedPath
	 * @return string[] indexed by parameter name
	 * @throws
	 */
	public static function getPathParametersForPaths($requestPath, $namedPath) {
		if (substr($requestPath, 0, 1) != "/") throw new Exception("Cannot call request path that doesn't lead with slash: " . $requestPath);
		if (substr($namedPath, 0, 1) != "/") throw new Exception("Swagger Error: cannot parse path location that doesn't lead with slash: " . $namedPath);

		$namedPathParts = explode("/", substr($namedPath, 1));
		$namedPathPartsCount = count($namedPathParts);
		$requestParts = explode("/", substr($requestPath, 1));
		$requestPartsCount = count($requestParts);

		// Number of parts must at least match
		if ($namedPathPartsCount != $requestPartsCount) throw new Exception('Mismatched path argument counts: ' . $requestPath . ' and ' . $namedPath);

		// Save a place to store path Arguments (if applicable)
		$parameters = array();

		for ($index = 0; $index < count($namedPathParts); $index++) {
			// If we are a Path Argument -- save to the parameters list
			if (substr($namedPathParts[$index][0], 0, 1) == '{') {
				$parameters[substr($namedPathParts[$index], 1, strlen($namedPathParts[$index]) - 2)] = $requestParts[$index];
			}
		}

		return $parameters;
	}

	public function __construct() {
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->pathParametersArray = array();
		$this->queryStringParametersArray = $_GET;
		$this->postParametersArray = $_POST;
		$this->fileParametersArray = $_FILES;
		$this->rawPost = file_get_contents("php://input");
		$this->headersArray = getallheaders();

		$requestParts = explode('?', $_SERVER['REQUEST_URI']);
		$this->path = $requestParts[0];

		if (array_key_exists('CONTEXT_PREFIX', $_SERVER) &&
			(strlen($prefix = $_SERVER['CONTEXT_PREFIX'])) &&
			(substr($this->path, 0, strlen($prefix)) == $prefix)) {
			$this->path = substr($this->path, strlen($prefix));
		}

		// Remove trailing slashes
		while (substr($this->path, strlen($this->path) - 1) == '/') $this->path = substr($this->path, 0, strlen($this->path) - 1);

		// Remove starting double-slashes
		while (substr($this->path, 0, 2) == '//') $this->path = substr($this->path, 1);
	}

	public function setPathParametersArray($pathParametersArray) {
		$this->pathParametersArray = $pathParametersArray;
	}

	public function setPathParametersArrayForNamedPath($namedPath) {
		$this->setPathParametersArray(self::getPathParametersForPaths($this->path, $namedPath));
	}
}