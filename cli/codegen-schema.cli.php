<?php
	// Setup the Parameters for codegen
	$objParameters = new QCliParameterProcessor('codegen-schema', 'Qcodo ORM Model Code Generator for JSON Schemas v' . QCODO_VERSION);

	// Add new default parameter -- db index
	$objParameters->AddDefaultParameter('swagger_path', QCliParameterType::Path, 'the path to the Swagger file which has Schema definitions being codegenned');

	$objParameters->Run();
	if (!is_file($path = $objParameters->GetDefaultValue('swagger_path'))) {
		print ("error: swagger file not found: " . $path . "\r\n");
		exit(1);
	}

	$swaggerText = file_get_contents($path);

	if (!is_dir($schemaPath = __APPLICATION__ . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Schema')) QApplicationBase::MakeDirectory($schemaPath, 0777);
	if (!is_dir($schemaGeneratedPath = __APPLICATION__ . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Schema' . DIRECTORY_SEPARATOR . 'generated')) QApplicationBase::MakeDirectory($schemaGeneratedPath, 0777);



	function GetPhpDocPropertyForProperty($property) {
		$ref = '$ref';
		if (isset($property->$ref)) return str_replace('#/definitions/', null, $property->$ref) . '';

		switch ($property->type) {
			case 'array':
				return GetPhpDocPropertyForProperty($property->items) . '[]';
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
			default:
				throw new Exception('Unhandled GetPhpDocPropertyForProperty: ' . $property->type);
		}
	}

	function GetModelDefinitionForProperty($property) {
		$ref = '$ref';
		if (isset($property->$ref)) return "'" . str_replace('#/definitions/', null, $property->$ref) . '' . "'";

		switch ($property->type) {
			case 'array':
				return "array('" . GetPhpDocPropertyForProperty($property->items) . "')";
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

	$swagger = json_decode($swaggerText);
	$templateClass = file_get_contents(dirname(__FILE__) . '/template-schema-class.txt');
	$templateGenerated = file_get_contents(dirname(__FILE__) . '/template-schema-generated.txt');
	foreach ($swagger->definitions as $schemaName => $schema) {
		$comment = array();
		$model = array();
		$resultParametersEnum = array();

		if (!isset($schema->properties)) throw new Exception('No properties defined on: ' . $schemaName);
		foreach ($schema->properties as $propertyName => $property) {
			if (isset($property->description) && $property->description)
				$comment[] = sprintf('	 * @property %s $%s %s', GetPhpDocPropertyForProperty($property), ucfirst($propertyName), $property->description);
			else
				$comment[] = sprintf('	 * @property %s $%s', GetPhpDocPropertyForProperty($property), ucfirst($propertyName));
			$model[] = sprintf('			\'%s\' => %s,', $propertyName, GetModelDefinitionForProperty($property));

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
			implode("\r\n", $comment),
			ucfirst($schemaName) . 'Gen',
			implode("\r\n", $model),
			count($resultParametersEnum) ? sprintf("\r\n		// ResultParameter Order By Enums\r\n%s\r\n", implode("\r\n", $resultParametersEnum)) : null,
			ucfirst($schemaName),
			ucfirst($schemaName),
			ucfirst($schemaName),
			ucfirst($schemaName)
		);

		file_put_contents($schemaGeneratedPath . DIRECTORY_SEPARATOR . ucfirst($schemaName) . 'Gen.php', $rendered);

		$rendered = sprintf($templateClass,
			QApplicationBase::$application->rootNamespace,
			QApplicationBase::$application->rootNamespace,
			ucfirst($schemaName) . 'Gen',
			ucfirst($schemaName),
			ucfirst($schemaName) . 'Gen',
			ucfirst($schemaName),
			ucfirst($schemaName) . 'Gen'
		);

		$path = $schemaPath . DIRECTORY_SEPARATOR . ucfirst($schemaName) . '.php';
		if (!file_exists($path)) file_put_contents($path, $rendered);
	}
