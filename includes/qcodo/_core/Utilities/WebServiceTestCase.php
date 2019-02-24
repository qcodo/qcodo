<?php

namespace Qcodo\Utilities;
use PHPUnit\Framework\TestCase;
use QJsonBaseClass;

abstract class WebServiceTestCase extends TestCase {
	/**
	 * @var HttpClient $httpClient
	 */
	protected $httpClient;

	/**
	 * @param string $baseUrl includes the scheme (http or https) and the base domain of all subsequent requests
	 */
	public function setUpHttpClient($baseUrl) {
		$this->httpClient = new HttpClient($baseUrl);
	}

	/**
	 * @param string $method the webservice method we are calling
	 * @param QJsonBaseClass|array $json the JSON Schema object we are POSTING
	 * @param integer $statusCode the expected HTTP Status Code
	 * @param string $schemaClass optional -- the expected JSON Schema Class, or NULL if nothing is expected
	 * @return QJsonBaseClass or null
	 */
	public function postJson($method, $json, $statusCode, $schemaClass = null) {
		$this->httpClient->postJson($method, $json);
		$this->assertEquals($statusCode, $this->httpClient->latestResponseStatusCode, 'HTTP Status Code Mismatch');

		if ($schemaClass)
			return $schemaClass::JsonDecode($this->httpClient->latestResponseBody);
		else
			return null;
	}

	/**
	 * @param integer $statusCode the expected HTTP Status Code
	 * @param string $schemaClass optional -- the expected JSON Schema Class, or NULL if nothing is expected
	 * @return QJsonBaseClass or null
	 */
	public function get($method, $statusCode, $schemaClass = null) {
		$this->httpClient->get($method);
		$this->assertEquals($statusCode, $this->httpClient->latestResponseStatusCode, 'HTTP Status Code Mismatch');

		if ($schemaClass)
			return $schemaClass::JsonDecode($this->httpClient->latestResponseBody);
		else
			return null;
	}
}