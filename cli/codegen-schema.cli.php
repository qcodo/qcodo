<?php
	// Setup the Parameters for codegen
	$objParameters = new QCliParameterProcessor('codegen-schema', 'Qcodo ORM Model Code Generator for JSON Schemas v' . QCODO_VERSION);

	// Add new default parameter -- db index
	$objParameters->AddDefaultParameter('swagger_path', QCliParameterType::Path, 'the path to the Swagger file which has Schema definitions being codegenned');

	// Add an optional flag -- csv -- this is to report
	$objParameters->AddFlagParameter('c', 'csv', 'generate only a CSV report of the paths');

	$objParameters->Run();
	if (!is_file($path = $objParameters->GetDefaultValue('swagger_path'))) {
		print ("error: swagger file not found: " . $path . "\n");
		exit(1);
	}

	$codegenSchema = new CodegenSchema(file_get_contents($path));

	if ($objParameters->GetValue('csv')) {
		$rowArray = $codegenSchema->GeneratePathReport();
		print QApplicationBase::generateCsvContent($rowArray);
	} else {
		$codegenSchema->GenerateSchema();
		$codegenSchema->GenerateClient();
	}


class CodegenSchema {
	protected $swagger;
	protected $schemaPath;
	protected $schemaGeneratedPath;
	protected $clientPath;
	protected $clientGeneratedPath;

	/**
	 * @param string $swaggerText
	 */
	public function __construct($swaggerText) {
		$this->swagger = json_decode($swaggerText);
		if (!$this->swagger) {
			exit("invalid swagger format\r\n");
		}

		$this->schemaPath = __APPLICATION__ . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Schema';
		$this->schemaGeneratedPath = __APPLICATION__ . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Schema' . DIRECTORY_SEPARATOR . 'generated';

		$this->clientPath = __APPLICATION__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'WebService';
		$this->clientGeneratedPath = __APPLICATION__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'WebService' . DIRECTORY_SEPARATOR . 'generated';

		// Create Directories
		foreach (array($this->schemaPath, $this->schemaGeneratedPath, $this->clientPath, $this->clientGeneratedPath) as $path) {
			if (!is_dir($path)) QApplicationBase::MakeDirectory($path, 0777);
		}
	}

	/**
	 * @return string[][]
	 */
	public function GeneratePathReport() {
		$rowArray = array(array(
			'',
			'Section',
			'URL Path',
			'HTTP Method',
			'Operation Method Name',
			'Summary'
		));
		foreach ($this->swagger->paths as $pathName => $path) {
			foreach ($path as $methodName => $definition) {
				$operationParts = explode('::', $definition->operationId);
				$className = $operationParts[0];
				$phpMethodName = $operationParts[1];

				$missing = null;
				try {
					$reflection = new ReflectionClass(QApplicationBase::$application->rootNamespace . '\\Handlers\\WebService\\' . $className);
					$reflection->getMethod($phpMethodName);
				} catch (ReflectionException $exception) {
					$missing = 'MISSING';
				}

				$rowArray[] = array(
					$missing,
					$definition->tags[0],
					$pathName,
					$methodName,
					$definition->operationId,
					$definition->summary
				);
			}
		}

		return $rowArray;
	}

	/**
	 * @param string $property
	 * @return string
	 */
	protected static function GetPhpDocPropertyForProperty($property) {
		$ref = '$ref';
		if (isset($property->$ref)) return str_replace('#/definitions/', '', $property->$ref) . '';

		switch ($property->type) {
			case 'array':
				return self::GetPhpDocPropertyForProperty($property->items) . '[]';
			case 'integer':
				return 'integer';
			case 'boolean':
				return 'boolean';
			case 'number':
				return 'float';
			case 'object':
				return 'stdClass';
			case 'string':
				if (isset($property->format)) {
					switch ($property->format) {
						case 'date':
							return 'QDateTime';
						case 'date-time':
							return 'QDateTime';
					}
				}
				return 'string';
			case 'file':
				return 'CURLFile';
			default:
				throw new Exception('Unhandled GetPhpDocPropertyForProperty: ' . $property->type);
		}
	}

	/**
	 * @param string $property
	 * @return string
	 */
	protected static function GetModelDefinitionForProperty($property) {
		$ref = '$ref';
		if (isset($property->$ref)) return "'" . str_replace('#/definitions/', '', $property->$ref) . '' . "'";

		switch ($property->type) {
			case 'array':
				return "array(" . self::GetModelDefinitionForProperty($property->items) . ")";
			case 'integer':
				return "'integer'";
			case 'boolean':
				return "'boolean'";
			case 'number':
				return "'float'";
			case 'object':
				return "'stdClass'";
			case 'string':
				if (isset($property->format)) {
					switch ($property->format) {
						case 'date':
							return "'date'";
						case 'date-time':
							return "'datetime'";
					}
				}
				return "'string'";
			default:
				throw new Exception('Unhandled GetModelDefinitionForProperty: ' . $property->type);
		}
	}

	public function GenerateSchema() {
		$templateClass = file_get_contents(dirname(__FILE__) . '/templates/schema-class.txt');
		$templateGenerated = file_get_contents(dirname(__FILE__) . '/templates/schema-generated.txt');

		foreach ($this->swagger->definitions as $schemaName => $schema) {
			$this->GenerateSchema_Helper($schema, $schemaName, $templateClass, $templateGenerated);
		}
	}

	/**
	 * @param stdClass $schema
	 * @param string $schemaName
	 * @param string $templateClass
	 * @param string $templateGenerated
	 */
	protected function GenerateSchema_Helper(stdClass $schema, $schemaName, $templateClass, $templateGenerated) {
		$comment = array();
		$model = array();
		$resultParametersEnum = array();

		$getSchemaLineArray = array();
		$updateFromSchemaLineArray = array();

		if (!isset($schema->properties)) throw new Exception('No properties defined on: ' . $schemaName);
		foreach ($schema->properties as $propertyName => $property) {
			if (isset($property->description) && $property->description)
				$comment[] = sprintf('	 * @property %s $%s %s', self::GetPhpDocPropertyForProperty($property), ucfirst($propertyName), $property->description);
			else
				$comment[] = sprintf('	 * @property %s $%s', self::GetPhpDocPropertyForProperty($property), ucfirst($propertyName));
			$model[] = sprintf('			\'%s\' => %s,', $propertyName, self::GetModelDefinitionForProperty($property));

			$getSchemaLineArray[] = sprintf('			$%s->%s = $this->%s;', lcfirst($schemaName), ucfirst($propertyName), ucfirst($propertyName));
			$updateFromSchemaLineArray[] = sprintf('			if ($%s->IsPropertySet(\'%s\'))		$this->%s = $%s->%s;',
				lcfirst($schemaName),
				$propertyName,
				ucfirst($propertyName),
				lcfirst($schemaName),
				ucfirst($propertyName)
			);

			// For ResultParameter properties, if the description is an enum like [Foo,Bar,Jaz]
			// Then we should codegen the enum options
			if ($propertyName == 'resultParameter') {
				$description = trim($property->description);
				if ((substr($description, 0, 1) == '[') && (substr($description, strlen($description) - 1) == ']')) {
					$description = substr($description, 1, strlen($description) - 2);
					foreach (explode(',', $description) as $enum) {
						$enum = trim($enum);
						if ($enum) {
							$resultParametersEnum[] = sprintf("		const OrderBy%s = '%s';",
								ucfirst($enum),
								strtolower($enum)
							);
						}
					}
				}
			}
		}

		$rendered = sprintf($templateGenerated,
			QApplicationBase::$application->rootNamespace,
			ucfirst($schemaName) . 'Gen',
			implode("\n", $comment),
			ucfirst($schemaName) . 'Gen',
			implode("\n", $model),
			count($resultParametersEnum) ? sprintf("\n		// ResultParameter Order By Enums\n%s\n", implode("\n", $resultParametersEnum)) : null,
			ucfirst($schemaName),
			ucfirst($schemaName),
			ucfirst($schemaName),
			ucfirst($schemaName),

			// GetSchema()
			ucfirst($schemaName),
			lcfirst($schemaName),
			ucfirst($schemaName),
			implode("\n", $getSchemaLineArray),
			lcfirst($schemaName),

			// UpdateFromSchema()
			ucfirst($schemaName),
			lcfirst($schemaName),
			ucfirst($schemaName),
			lcfirst($schemaName),
			implode("\n", $updateFromSchemaLineArray)
		);

		file_put_contents($this->schemaGeneratedPath . DIRECTORY_SEPARATOR . ucfirst($schemaName) . 'Gen.php', $rendered);

		$rendered = sprintf($templateClass,
			QApplicationBase::$application->rootNamespace,
			QApplicationBase::$application->rootNamespace,
			ucfirst($schemaName) . 'Gen',
			ucfirst($schemaName),
			ucfirst($schemaName) . 'Gen',
			ucfirst($schemaName),
			ucfirst($schemaName) . 'Gen'
		);

		$path = $this->schemaPath . DIRECTORY_SEPARATOR . ucfirst($schemaName) . '.php';
		if (!file_exists($path)) file_put_contents($path, $rendered);
	}

	public function GenerateClient() {
		$this->GenerateClient_SuperClass();

		// Clients
		$clientObjectArray = array();

		foreach ($this->swagger->paths as $path => $methods) {
			foreach ($methods as $method => $apiDefinition) {
				$operationId = $apiDefinition->operationId;
				if (!$operationId) throw new Exception(sprintf('%s at %s has no operationId', $method, $path));

				$operationParts = explode('::', $operationId);
				if (count($operationParts) != 2) throw new Exception(sprintf('%s at %s has an invalid operationId: %s', $method, $path, $operationId));

				$clientName = $operationParts[0];
				$methodName = $operationParts[1];

				if (!array_key_exists($clientName, $clientObjectArray)) $clientObjectArray[$clientName] = array();

				$clientObjectArray[$clientName][$methodName] = $apiDefinition;
				$apiDefinition->path = $path;
				$apiDefinition->method = $method;
			}
		}

		foreach ($clientObjectArray as $clientName => $clientObject) {
			$this->GenerateClient_ProxyClass($clientName, $clientObject);
		}

		$this->GenerateClient_BaseClass($clientObjectArray);
	}

	protected function GenerateClient_ProxyClass($clientName, $clientObject) {
		$path = $this->clientGeneratedPath . DIRECTORY_SEPARATOR . $clientName . 'Client.php';

		$responseClassArray = array();
		$methodArray = array();

		foreach ($clientObject as $methodName => $apiDefinition) {
			$responseClassArray[] = $this->GenerateClient_ProxyClass_ResponseClass($clientName, $methodName, $apiDefinition);
			$methodArray[] = $this->GenerateClient_ProxyClass_Method($clientName, $methodName, $apiDefinition);
		}

		$template = file_get_contents(dirname(__FILE__) . '/templates/webservice-client-proxy.txt');
		$rendered = sprintf($template,
			QApplicationBase::$application->rootNamespace,
			QApplicationBase::$application->rootNamespace,
			QApplicationBase::$application->rootNamespace,
			implode("\n\n", $responseClassArray),
			$clientName,
			implode("\n\n", $methodArray));

		file_put_contents($path, $rendered);
	}

	protected function GenerateClient_ProxyClass_Method($clientName, $methodName, $apiDefinition) {
		$phpDocArray = array();
		$parameterArray = array();
		$caseArray = array();

		$requestPayloadSetupQuery = null;
		$requestPayloadSetupForm = null;

		$urlDefinition = sprintf("'%s'", $apiDefinition->path);
		$apiRequest = null;
		$isFormData = false;
		$isJsonBody = false;

		if (isset($apiDefinition->parameters)) {
			foreach ($apiDefinition->parameters as $parameterDefinition) {
				$parameterName = str_replace('_', ' ', $parameterDefinition->name);
				$parameterName = ucwords($parameterName);
				$parameterName = str_replace(' ', '', $parameterName);
				$parameterName = lcfirst($parameterName);

				$phpDocProperty = null;

				switch ($parameterDefinition->in) {
					case 'query':
						if (!$requestPayloadSetupQuery) $requestPayloadSetupQuery = "\n\t\t\$queryArray = [];\n";
						$requestPayloadSetupQuery .= "\t\tif (strlen(trim((string) $" . $parameterName . '))) ' .
							'$queryArray[] = \'' . $parameterDefinition->name . "=' . urlencode($" . $parameterName . ");\n";
						$phpDocProperty = self::GetPhpDocPropertyForProperty($parameterDefinition);
						$parameterArray[] = '$' . $parameterName;
						break;
					case 'formData':
						if ($isJsonBody) throw new Error('Cannot have both body and formData in the same request: ' . $methodName);
						$isFormData = true;

						$apiRequest = ', $postFieldsArray, \'form\'';
						if (!$requestPayloadSetupForm) $requestPayloadSetupForm = "\n\t\t\$postFieldsArray = [];\n";

						$parameterType = trim(strtolower($parameterDefinition->type));
						switch ($parameterType) {
							case 'string':
							case 'number':
							case 'integer':
								$requestPayloadSetupForm .= "\t\tif (!is_null($" . $parameterName . ')) ' .
									'$postFieldsArray[\'' . $parameterDefinition->name . "'] = $" . $parameterName . ";\n";
								$phpDocProperty = self::GetPhpDocPropertyForProperty($parameterDefinition);
								$parameterArray[] = '$' . $parameterName;
								break;

							case 'file':
								$requestPayloadSetupForm .= "\t\tif (!is_null($" . $parameterName . ')) ' .
									'$postFieldsArray[\'' . $parameterDefinition->name . "'] = $" . $parameterName . ";\n";
								$phpDocProperty = self::GetPhpDocPropertyForProperty($parameterDefinition);
								$parameterArray[] = '$' . $parameterName;
								break;

							default:
								throw new Exception('formData parameter type not supported: ' . $parameterType);
						}

						break;
					case 'path':
						$phpDocProperty = self::GetPhpDocPropertyForProperty($parameterDefinition);
						$parameterArray[] = '$' . $parameterName;
						$urlDefinition = str_replace(
							'{' . $parameterDefinition->name . '}',
							"' . \n\t\t\t(strlen((string) $" . $parameterName . ") ? urlencode($" . $parameterName . ") : '') . '",
							$urlDefinition
						);
						break;
					case 'body':
						if ($isFormData) throw new Error('Cannot have both body and formData in the same request: ' . $methodName);
						$isJsonBody = true;

						$apiRequest = sprintf(", $%s, 'json'", $parameterName);
						$phpDocProperty = 'Schema\\' . self::GetPhpDocPropertyForProperty($parameterDefinition->schema);

						$length = strlen($phpDocProperty);
						if (substr($phpDocProperty, $length-2) == '[]') {
							$parameterArray[] = '$' . $parameterName;
						} else {
							$parameterArray[] = $phpDocProperty . ' $' . $parameterName;
						}
						break;
					default:
						throw new Exception('Unhandled IN parameterDefinition: ' . $methodName);
				}

				$phpDocArray[] = sprintf('	 * @param %s $%s', $phpDocProperty, $parameterName);
			}
		}

		if ($requestPayloadSetupQuery) {
			$urlDefinition .= " .\n\t\t\t(count(\$queryArray) ? '?' . implode('&', \$queryArray) : null)";
		}

		foreach ($apiDefinition->responses as $statusCode => $responseDefinition) {
			$decorator = 'trim';
			if (isset($responseDefinition->schema)) {
				$phpDocProperty = self::GetPhpDocPropertyForProperty($responseDefinition->schema);
				$length = strlen($phpDocProperty);
				if (substr($phpDocProperty, $length-2) == '[]') {
					// Array
					$phpDocProperty = substr($phpDocProperty, 0, $length - 2);
					$decorator = 'Schema\\' . $phpDocProperty . '::JsonDecodeArray';
				} else {
					$decorator = 'Schema\\' . $phpDocProperty . '::JsonDecode';
				}
			}

			$caseArray[] = sprintf('		case %s: $response->status%s = %s($this->client->LastResponseBody); break;',
				$statusCode,
				$statusCode,
				$decorator
			);
		}

		$template = file_get_contents(dirname(__FILE__) . '/templates/webservice-client-proxy-method.txt');
		$rendered = sprintf($template,
			implode("\n", $phpDocArray),
			$clientName,
			ucfirst($methodName),
			$methodName,
			implode(', ', $parameterArray),

			$requestPayloadSetupQuery,
			str_replace(" . ''", '', $urlDefinition),
			$requestPayloadSetupForm,

			$apiDefinition->method,
			$apiRequest,

			$clientName,
			ucfirst($methodName),
			implode("\n", $caseArray));

		return $rendered;
	}

	protected function GenerateClient_ProxyClass_ResponseClass($clientName, $methodName, $apiDefinition) {
		$propertyArray = array();

		foreach ($apiDefinition->responses as $statusCode => $responseDefinition) {

			if (isset($responseDefinition->schema)) {
				$type = 'Schema\\' . self::GetPhpDocPropertyForProperty($responseDefinition->schema);
			} else {
				$type = 'string';
			}

			$propertyArray[] = sprintf('	/**
	 * @var %s $status%s
	 */
	public $status%s;', $type, $statusCode, $statusCode);
		}

		$template = file_get_contents(dirname(__FILE__) . '/templates/webservice-client-proxy-responseclass.txt');
		$rendered = sprintf($template,
			$clientName,
			ucfirst($methodName),
			implode("\n\n", $propertyArray));

		return $rendered;
	}

	protected function GenerateClient_SuperClass() {
		$path = $this->clientPath . DIRECTORY_SEPARATOR . 'Client.php';
		if (file_exists($path)) return;

		$template = file_get_contents(dirname(__FILE__) . '/templates/webservice-client-php.txt');
		$rendered = sprintf($template,
			QApplicationBase::$application->rootNamespace,
			QApplicationBase::$application->rootNamespace);

		file_put_contents($path, $rendered);
	}

	/**
	 * @param array $clientObjectArray
	 */
	protected function GenerateClient_BaseClass($clientObjectArray) {
		$path = $this->clientGeneratedPath . DIRECTORY_SEPARATOR . 'ClientBase.php';

		$importListArray = array();
		$phpDocArray = array();
		$propertyArray = array();
		$getterArray = array();

		foreach ($clientObjectArray as $clientName => $clientObject) {
			$importListArray[] = sprintf("require(dirname(__FILE__) . '/%sClient.php');", $clientName);
			$phpDocArray[] = sprintf(' * @property-read Proxy\%sClient $%s', $clientName, $clientName);
			$propertyArray[] = sprintf('	/**
	 * @var Proxy\%sClient $%s
	 */
	protected $%s;', $clientName, lcfirst($clientName), lcfirst($clientName));
			$getterArray[] = sprintf('			case \'%s\':
				if (!$this->%s) $this->%s = new Proxy\%sClient($this);
				return $this->%s;', $clientName, lcfirst($clientName), lcfirst($clientName), $clientName, lcfirst($clientName));
		}

		$template = file_get_contents(dirname(__FILE__) . '/templates/webservice-clientbase-php.txt');
		$rendered = sprintf($template,
			QApplicationBase::$application->rootNamespace,
			QApplicationBase::$application->rootNamespace,
			implode("\n", $importListArray),
			implode("\n", $phpDocArray),
			implode("\n\n", $propertyArray),
			implode("\n", $getterArray));

		file_put_contents($path, $rendered);
	}
}
