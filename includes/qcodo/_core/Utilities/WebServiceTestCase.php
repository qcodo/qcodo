<?php

namespace Qcodo\Utilities;
use PHPUnit\Framework\TestCase;
use QJsonBaseClass;

abstract class WebServiceTestCase extends TestCase {
	/**
	 * @var WebServiceClient $webServiceClient
	 */
	protected $webServiceClient;

	/**
	 * @var boolean $exitOnNextCallFlag
	 */
	protected $exitOnNextFlag = false;

	/**
	 * Exit on the next WebService call, and output the raw response
	 */
	public function setExitOnNextFlag() {
		$this->outputFlag = true;
	}

	/**
	 * @param string $baseUrl includes the scheme (http or https) and the base domain of all subsequent requests
	 */
	public function setUpWebServiceClient($baseUrl) {
		$this->webServiceClient = new WebServiceClient($baseUrl);
	}

	/**
	 * @param string $method the webservice method we are calling
	 * @param QJsonBaseClass|array $json the JSON Schema object we are POSTING
	 * @param integer $statusCode the expected HTTP Status Code
	 * @return null
	 */
	public function postJson($method, $json, $statusCode) {
		$this->webServiceClient->postJson($method, $json);
		if ($this->exitOnNextFlag) exit('[' . $this->webServiceClient->lastResponseBody . "]\r\n");

		$this->assertEquals($statusCode, $this->webServiceClient->lastResponseStatusCode, 'HTTP Status Code Mismatch on [' . $method . '] - ' . $this->webServiceClient->lastResponseBody);
	}

	/**
	 * @param string $method the webservice method we are calling
	 * @param QJsonBaseClass|array $json the JSON Schema object we are POSTING
	 * @param integer $statusCode the expected HTTP Status Code
	 * @param string $schemaClass the expected JSON Schema Class
	 * @return QJsonBaseClass
	 */
	public function schemaFromPostJson($method, $json, $statusCode, $schemaClass) {
		$this->postJson($method, $json, $statusCode);

		$responseSchema = $schemaClass::JsonDecode($this->webServiceClient->lastResponseBody);
		$this->assertNotNull($responseSchema, 'Received [' . $this->webServiceClient->lastResponseBody . '] when expecting ' . $schemaClass);
		return $responseSchema;
	}

	/**
	 * @param string $method the webservice method we are calling
	 * @param QJsonBaseClass|array $json the JSON Schema object we are POSTING
	 * @param integer $statusCode the expected HTTP Status Code
	 * @param string $schemaClass the expected JSON Schema Class
	 * @return QJsonBaseClass[]
	 */
	public function schemaArrayFromPostJson($method, $json, $statusCode, $schemaClass) {
		$this->postJson($method, $json, $statusCode);

		$responseSchemaArray = $schemaClass::JsonDecodeArray($this->webServiceClient->lastResponseBody);
		$this->assertNotNull($responseSchemaArray, 'Received [' . $this->webServiceClient->lastResponseBody . '] when expecting ' . $schemaClass . '[]');
		$this->assertTrue(ArrayObject::IsArray($responseSchemaArray), 'Received [' . $this->webServiceClient->lastResponseBody . '] when expecting ' . $schemaClass . '[]');
		return $responseSchemaArray;
	}

	/**
	 * @param string $method the webservice method we are calling
	 * @param integer $statusCode the expected HTTP Status Code
	 * @return null
	 */
	public function get($method, $statusCode) {
		$this->webServiceClient->get($method);
		if ($this->exitOnNextFlag) exit('[' . $this->webServiceClient->lastResponseBody . "]\r\n");

		$this->assertEquals($statusCode, $this->webServiceClient->lastResponseStatusCode, 'HTTP Status Code Mismatch on [' . $method . '] - ' . $this->webServiceClient->lastResponseBody);
	}

	/**
	 * @param string $method the webservice method we are calling
	 * @param integer $statusCode the expected HTTP Status Code
	 * @return null
	 */
	public function delete($method, $statusCode) {
		$this->webServiceClient->get($method, 'DELETE');
		if ($this->exitOnNextFlag) exit('[' . $this->webServiceClient->lastResponseBody . "]\r\n");

		$this->assertEquals($statusCode, $this->webServiceClient->lastResponseStatusCode, 'HTTP Status Code Mismatch on [' . $method . '] - ' . $this->webServiceClient->lastResponseBody);
	}

	/**
	 * @param integer $statusCode the expected HTTP Status Code
	 * @param string $schemaClass the expected JSON Schema Class
	 * @return QJsonBaseClass
	 */
	public function schemaFromGet($method, $statusCode, $schemaClass) {
		$this->get($method, $statusCode);

		$responseSchema = $schemaClass::JsonDecode($this->webServiceClient->lastResponseBody);
		$this->assertNotNull($responseSchema, 'Received [' . $this->webServiceClient->lastResponseBody . '] when expecting ' . $schemaClass);
		return $responseSchema;
	}

	/**
	 * @param integer $statusCode the expected HTTP Status Code
	 * @param string $schemaClass the expected JSON Schema Class
	 * @return QJsonBaseClass[]
	 */
	public function schemaArrayFromGet($method, $statusCode, $schemaClass) {
		$this->get($method, $statusCode);

		$responseSchemaArray = $schemaClass::JsonDecodeArray($this->webServiceClient->lastResponseBody);
		$this->assertNotNull($responseSchemaArray, 'Received [' . $this->webServiceClient->lastResponseBody . '] when expecting ' . $schemaClass . '[]');
		$this->assertTrue(ArrayObject::IsArray($responseSchemaArray), 'Received [' . $this->webServiceClient->lastResponseBody . '] when expecting ' . $schemaClass . '[]');
		return $responseSchemaArray;
	}

	/**
	 * @param string $needle
	 * @param string $message
	 */
	public function assertLastResponseContains($needle, $message = '') {
		$this->assertContains($needle, $this->webServiceClient->lastResponseBody, $message, true);
	}
}
