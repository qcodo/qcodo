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



	/**
	 * Returns the original JSON from the raw file itself
	 * @return string
	 */
	public function getOriginalJson() {
		return file_get_contents($this->path);
	}



	/**
	 * Given a request path (which may or may not include path arguments), this will look for the appropriate
	 * named path (which will include path argument placeholders).  If there are specific arguments, this will
	 * return those in the array.
	 *
	 * @param string $requestPath
	 * @return string or null if not found
	 * @throws
	 */
	public function getNamedPathForRequestPath($requestPath) {
		if (!$requestPath) return null;
		if (substr($requestPath, 0, 1) != "/") throw new Exception("Cannot call request path that doesn't lead with slash: " . $requestPath);

		// Shortcut: Return an absolute match (if applicable)
		if (isset($this->swaggerObject->paths->$requestPath)) return $requestPath;

		$requestParts = explode("/", substr($requestPath, 1));
		$requestPartsCount = count($requestParts);

		foreach ($this->swaggerObject->paths as $path => $definition) {
			if (substr($path, 0, 1) != "/") throw new Exception("Swagger Error: cannot parse path location that doesn't lead with slash: " . $path);
			$pathParts = explode("/", substr($path, 1));
			$pathPartsCount = count($pathParts);

			// Number of parts must at least match
			if ($pathPartsCount != $requestPartsCount)  continue;

			for ($index = 0; $index < count($pathParts); $index++) {

				if (substr($pathParts[$index][0], 0, 1) == '{') {
					// We are a Path Argument -- let's move on...
				} else if ($pathParts[$index] == $requestParts[$index]) {
					// Parts are equal at this index -- let's move on...
				} else {
					// parts are NOT equal... let's leave this iteration
					continue 2;
				}
			}

			// If we are here, it means all parts match!
			return $path;
		}

		// If we are here, we have exhausted all paths in the swagger and didn't find a match
		$pathArgumentsArray = array();
		return null;
	}



	/**
	 * Maps to a specific ApiHandler class and method name for the given path and requestMethod
	 * @param string $namedPath
	 * @param string $requestMethod
	 * @return string[] first index is class name, second index is method name
	 * @throws
	 */
	public function getOperationForPathAndMethod($namedPath, $requestMethod) {
		if (!isset($this->swaggerObject->paths->$namedPath)) throw new Exception("Path Does Not Exist");
		if (!$this->isMethodExistsForPath($namedPath, $requestMethod)) throw new Exception("Method Not Defined");

		$requestMethod = trim(strtolower($requestMethod));
		$operation = $this->swaggerObject->paths->$namedPath->$requestMethod;

		// Ensure we have an Operation Identifier
		if (!isset($operation->operationId)) throw new Exception("No Operation Identifier Found");

		// Get the ApiHandler class name and method name
		$operationIdentifier = $operation->operationId;
		$operationIdentifierParts = explode("::", $operationIdentifier);
		if (count($operationIdentifierParts) != 2) throw new Exception("Malformed Operation Identifier: " . $operationIdentifier);
		$className = $operationIdentifierParts[0];
		$methodName = $operationIdentifierParts[1];

		return array($className, $methodName);
	}

	protected function isMethodExistsForPath($requestPath, $requestMethod) {
		if (!isset($this->swaggerObject->paths->$requestPath)) throw new Exception("Path Does Not Exist");
		$requestMethod = trim(strtolower($requestMethod));
		return isset($this->swaggerObject->paths->$requestPath->$requestMethod);
	}


	/**
	 * @return array[] array of arrays, where each is an array that contains path and operation
	 * @throws Exception
	 */
	private function reportUndefined() {
		$arrayToReturn = array();
		foreach ($this->swagger->paths as $path => $definitionJson) {
			foreach ($definitionJson as $requestMethod => $operation) {
				$operationIdentifier = $operation->operationId;
				$operationIdentifierParts = explode("::", $operationIdentifier);
				if (count($operationIdentifierParts) != 2) throw new Exception("Malformed Operation Identifier: " . $operationIdentifier);
				$className = $operationIdentifierParts[0];
				$methodName = $operationIdentifierParts[1];

				if (class_exists($className) && method_exists($className, $methodName)) {
				} else {
					$arrayToReturn[] = array($path, $requestMethod);
				}
			}
		}

		return $arrayToReturn;
	}





	/**
	 * Assumes an Example JSON schema already exists.  If not, this will throw.
	 *
	 * @param string $namedPath
	 * @param string $requestMethod
	 * @return string|\stdClass either a string for a message/description based output and example, or a stdClass object that can then be json_encode'd by the caller
	 * @throws Exception
	 */
	public function getExampleAtPathAndMethod($namedPath, $requestMethod) {
		if (!$this->isMethodExistsForPath($namedPath, $requestMethod)) throw new Exception("Method Not Defined");

		$requestMethod = trim(strtolower($requestMethod));
		$operation = $this->swaggerObject->paths->$namedPath->$requestMethod;

		$okResponse = "200";
		if (!isset($operation->responses) || !isset($operation->responses->$okResponse)) {
			throw new Exception("No 200 OK response defined");
		}

		// If no schema, return the description
		if (!isset($operation->responses->$okResponse->schema)) {
			return ($operation->responses->$okResponse->description);
		}

		// If response has an example, return that
		if (isset($operation->responses->$okResponse->schema->example)) {
			return $operation->responses->$okResponse->schema->example;
		}

		// Otherwise, check into the $ref
		$ref = '$ref';
		if (isset($operation->responses->$okResponse->schema->$ref)) {
			$schemaName = $operation->responses->$okResponse->schema->$ref;
			$schemaName = str_replace('#/definitions/', '', $schemaName);
			if (!isset($this->swaggerObject->definitions->$schemaName)) throw new Exception("Schema Not Defined: " . $operation->responses->$okResponse->schema->$ref);
			if (isset($this->swaggerObject->definitions->$schemaName->example)) {
				return $this->swaggerObject->definitions->$schemaName->example;
			}
		}

		throw new Exception("No example to mock");
	}
}
