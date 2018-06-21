<?php

// Setup the Parameters for codegen
$objParameters = new QCliParameterProcessor('schema-generate', 'Qcodo Swagger Schema Generator v' . QCODO_VERSION);

// Add new default parameter -- db index
$objParameters->AddDefaultParameter('db_index', QCliParameterType::String, 'the DB Index name in configuration/database.php which is being codegenned');

// Optional Parameters for Path to Codegen Settings
$objParameters->Run();


function GetForeignKey($columnName, $foreignKeyArray) {
	foreach ($foreignKeyArray as $foreignKey) {
		if ((count($foreignKey->ColumnNameArray) == 1) && ($columnName == $foreignKey->ColumnNameArray[0])) return $foreignKey;
	}

	return null;
}

function Pluralize($strName) {
	// Special Rules go Here
	switch (true) {
		case (strtolower($strName) == 'play'):
			return $strName . 's';
	}

	$intLength = strlen($strName);
	if (substr($strName, $intLength - 1) == "y")
		return substr($strName, 0, $intLength - 1) . "ies";
	if (substr($strName, $intLength - 1) == "s")
		return $strName . "es";
	if (substr($strName, $intLength - 1) == "x")
		return $strName . "es";
	if (substr($strName, $intLength - 1) == "z")
		return $strName . "zes";
	if (substr($strName, $intLength - 2) == "sh")
		return $strName . "es";
	if (substr($strName, $intLength - 2) == "ch")
		return $strName . "es";

	return $strName . "s";
}

$database = QApplicationBase::$application->getDatabase($objParameters->GetDefaultValue('db_index'));
$tableArray = $database->GetTables();

foreach ($tableArray as $tableName) {
	if (QString::FirstCharacter($tableName) == '_') {
		// Ignore
		continue;
	}

	switch (substr($tableName, strlen($tableName) - 5)) {
		case '_assn':
			continue 2;
		case '_type':
			continue 2;
	}

	$output = sprintf(<<<EOT
	"%s": {
		"type":		"object",
		"properties": {

EOT
		, QConvertNotation::CamelCaseFromUnderscore($tableName));

	// Foreign Keys
	$foreignKeyArray = $database->GetForeignKeysForTable($tableName);


	// Properties
	$propertyArray = array();
	foreach ($database->GetFieldsForTable($tableName) as $databaseField) {
		$foreignKey = GetForeignKey($databaseField->Name, $foreignKeyArray);
		$propertyName = QConvertNotation::JavaCaseFromUnderscore($databaseField->Name);
		$propertyFormat = null;
		$propertyEnumArray = null;

		if ($foreignKey && (substr($foreignKey->ReferenceTableName, strlen($foreignKey->ReferenceTableName) - 5) == "_type")) {
			$propertyType = 'string';
			$propertyEnumArray = array();
			$result = $database->Query("SELECT name FROM " . $foreignKey->ReferenceTableName . " ORDER BY id");
			while ($row = $result->GetNextRow()) $propertyEnumArray[] = '"' . QConvertNotation::CamelCaseFromUnderscore(str_replace(' ', '_', $row->GetColumn('name'))) . '"';

			// We're a type column -- not really a foreign key
			$foreignKey = null;
		} else {
			switch ($databaseField->Type) {
				case QDatabaseFieldType::Bit:
					$propertyType = 'boolean';
					break;
				case QDatabaseFieldType::Blob:
					$propertyType = 'string';
					break;
				case QDatabaseFieldType::Char:
					$propertyType = 'string';
					break;
				case QDatabaseFieldType::Date:
					$propertyType = 'string';
					$propertyFormat = 'date';
					break;
				case QDatabaseFieldType::DateTime:
					$propertyType = 'string';
					$propertyFormat = 'date-time';
					break;
				case QDatabaseFieldType::Float:
					$propertyType = 'number';
					break;
				case QDatabaseFieldType::Integer:
					$propertyType = 'integer';
					if (($databaseField->PrimaryKey) || $foreignKey) {
						$propertyFormat = 'int64';
					}
					break;
				case QDatabaseFieldType::VarChar:
					$propertyType = 'string';
					break;
				default:
					throw new Exception("Unsupported Type: " . $databaseField->Type);
			}
		}

		if ($propertyFormat) {
			$propertyArray[] = sprintf('				"%s": {"type": "%s", "format": "%s"}', $propertyName, $propertyType, $propertyFormat);
		} else if ($propertyEnumArray) {
			$propertyArray[] = sprintf('				"%s": {"type": "%s", "enum": [%s]}', $propertyName, $propertyType, implode(', ', $propertyEnumArray));
		} else {
			$propertyArray[] = sprintf('				"%s": {"type": "%s"}', $propertyName, $propertyType);
		}

		if ($foreignKey) {
			$propertyArray[] = sprintf('				"%s": {"$ref": "#/definitions/%s"}', substr($propertyName, 0, strlen($propertyName) - 2), QConvertNotation::CamelCaseFromUnderscore($foreignKey->ReferenceTableName));
		}
	}

	foreach ($tableArray as $reverseReferenceTableName) {
		if (QString::FirstCharacter($tableName) == '_') {
			// Ignore
			continue;
		}

		switch (substr($tableName, strlen($tableName) - 5)) {
			case '_assn':
				continue 2;
			case '_type':
				continue 2;
		}

		foreach ($database->GetForeignKeysForTable($reverseReferenceTableName) as $foreignKey) {
			if ($foreignKey->ReferenceTableName == $tableName) {
				$propertyArray[] = sprintf('				"%s": {"type": "array", "items": { "$ref": "#/definitions/%s" }}',
					Pluralize(QConvertNotation::JavaCaseFromUnderscore($reverseReferenceTableName)),
					QConvertNotation::CamelCaseFromUnderscore($reverseReferenceTableName));
			}
		}
	}

	$output .= implode(",\r\n", $propertyArray) . "\r\n";
	$output .= "			}
	}";

	print $output . "\r\n";
}
