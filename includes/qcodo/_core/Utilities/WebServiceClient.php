<?php

namespace Qcodo\Utilities;
use QBaseClass;
use Exception;
use QJsonBaseClass;

class WebServiceClient extends QBaseClass {
	public $lastResponseBody;
	public $lastResponseHeadersArray;
	public $lastResponseStatusCode;

	public $requestHeadersArray;
	public $baseUrl;

	/**
	 * WebServiceClient constructor
	 * @param string $baseUrl includes the scheme (http or https) and the base domain of all subsequent requests
	 */
	public function __construct($baseUrl) {
		$this->baseUrl = $baseUrl;
		$this->requestHeadersArray = array();
	}

	/**
	 * @param string $type e.g. Bearer, Basic, etc.
	 * @param string $credentials the hashed credentials
	 */
	public function setAuthorizationHeader($type, $credentials) {
		$this->setRequestHeader('Authorization', sprintf('%s %s', $type, $credentials));
	}

	public function setRequestHeader($name, $value = null) {
		if (!is_null($value))
			$this->requestHeadersArray[$name] = $value;
		else {
			$this->requestHeadersArray[$name] = null;
			unset($this->requestHeadersArray[$name]);
		}
	}

	/**
	 * @param string $method the webservice method we are calling
	 * @param QJsonBaseClass|array $json the JSON Schema object we are POSTING
	 * @throws \QCallerException
	 */
	public function postJson($method, $json) {
		if (substr($method, 0, 1) != '/') throw new Exception('Method must begin with a /');

		if ($json instanceof QJsonBaseClass) {
			$content = $json->JsonEncode();
		} else if (ArrayObject::IsArray($json)) {
			$content = QJsonBaseClass::JsonEncodeArray($json);
		} else if (is_null($json)) {
			$content = null;
		} else {
			throw new Exception('Must be a JsonBaseClass or Array');
		}

		$this->setRequestHeader('Content-Type', 'application/json');

		$curl = curl_init($this->baseUrl . $method);

		$headerArray = array();
		foreach ($this->requestHeadersArray as $name => $value) {
			$headerArray[] = sprintf('%s: %s', $name, $value);
		}

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

		$result = curl_exec($curl);
		$this->lastResponseBody = $result;

		$statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		$this->lastResponseStatusCode = $statusCode;

		curl_close($curl);
	}

	public function get($method, $customRequest = null) {
		if (substr($method, 0, 1) != '/') throw new Exception('Method must begin with a /');

		$this->setRequestHeader('Content-Type');

		$curl = curl_init($this->baseUrl . $method);

		$headerArray = array();
		foreach ($this->requestHeadersArray as $name => $value) {
			$headerArray[] = sprintf('%s: %s', $name, $value);
		}

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

		if ($customRequest) {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $customRequest);
		}

		$result = curl_exec($curl);
		$this->lastResponseBody = $result;

		$statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		$this->lastResponseStatusCode = $statusCode;

		curl_close($curl);
	}
}
