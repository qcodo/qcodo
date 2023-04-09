<?php

namespace Qcodo\Handlers\Console;
use \Exception;
use Qcodo\Handlers;
use \QApplicationBase;
use \QDatabaseBase;
use \QDatabaseFieldType;
use \QString;
use \QConvertNotation;

class Qcodo extends Handlers\Console {
	public function Run() {
		$this->executeMethodList();
	}

	/**
	 * @param string $index the db index to generate
	 * @param string $command should be codegen, schema or pdf
	 * @param string $output if command is PDF, the path/folder to output the PDF to
	 * @return void
	 */
	public function Database($index, $command, $output = null) {
		if (!array_key_exists($index, QApplicationBase::$application->configuration['database'])) {
			exit("db index not found: " . $index . "\n");
		}

		self::SetupDatabaseIndexForCodegen($index);
		$database = QApplicationBase::$application->getDatabase($index);

		switch (trim(strtolower($command))) {
			case 'codegen':
				exit("not yet implemented\n");

			case 'schema':
				$this->Database_Schema($database);
				break;

			case 'pdf':
				if (!$output || !is_dir($output)) exit("path not found: " . $output . "\n");
				$this->Database_Pdf($database, $output . '/' . $index . '.html');
				break;
		}
	}

	private static function GetForeignKey($columnName, $foreignKeyArray) {
		foreach ($foreignKeyArray as $foreignKey) {
			if ((count($foreignKey->ColumnNameArray) == 1) && ($columnName == $foreignKey->ColumnNameArray[0])) return $foreignKey;
		}

		return null;
	}

	private static function Pluralize($strName) {
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



	private static function SetupDatabaseIndexForCodegen($strDbIndex) {
		$configuration = QApplicationBase::$application->configuration['database'][$strDbIndex];
		if (array_key_exists('databaseCodegen', $configuration)) {
			$configuration['database'] = $configuration['databaseCodegen'];
			QApplicationBase::$application->database[$strDbIndex] = QApplicationBase::$application->createDatabaseConnection($configuration, $strDbIndex);
		}
	}

	private function Database_Pdf(QDatabaseBase $database, $path) {
		$tableArray = $database->GetTables();
		sort($tableArray);

		// Get Types First
		$typeArrayByTableName = array();
		foreach ($tableArray as $tableName) {
			if (QString::FirstCharacter($tableName) == '_') continue; // Ignore
			if (substr($tableName, strlen($tableName) - 5) != '_type') continue; // Not Type

			$typeArrayByTableName[$tableName] = array();
			$result = $database->Query("SELECT name FROM " . $tableName . " ORDER BY id");
			while ($row = $result->GetNextRow()) $typeArrayByTableName[$tableName][] = $row->GetColumn('name');
		}

		// Iterate thru all
		$htmlArrayByTable = array();
		foreach ($tableArray as $tableName) {
			if (QString::FirstCharacter($tableName) == '_') continue; // Ignore
			if (substr($tableName, strlen($tableName) - 5) == '_type') continue; // Ignore
			if (substr($tableName, strlen($tableName) - 5) == '_assn') continue; // Ignore

			$html = '<h2>' . $tableName . '</h2>';
			$html .= '<table><thead>';
			$html .= '<tr><th style="width: 50%;">Column</th><th style="width: 15%;">Type</th><th style="width: 15%;">Flags</th><th style="width: 30%;">Reference</th></tr>';
			$html .= '</thead><tbody>';

			$foreignKeyByColumnName = array();
			foreach ($database->GetForeignKeysForTable($tableName) as $foreignKey) {
				$foreignKeyByColumnName[$foreignKey->ColumnNameArray[0]] = $foreignKey;
			}

			foreach ($database->GetFieldsForTable($tableName) as $field) {
				$foreignKey = array_key_exists($field->Name, $foreignKeyByColumnName) ? $foreignKeyByColumnName[$field->Name] : null;

				$flagArray = array();
				if ($field->Unique) $flagArray[] = '<span>U</span>';
				if ($field->Indexed) $flagArray[] = '<span>i</span>';
				if ($field->Unsigned) $flagArray[] = '<span>&plusmn;</span>';
				if ($field->Identity) $flagArray[] = '<span>A</span>';
				if ($field->NotNull) $flagArray[] = '<span>N</span>';
				if ($field->PrimaryKey) $flagArray[] = '<span>P</span>';
				if ($foreignKey) $flagArray[] = '<span>F</span>';

				$html .= '<tr>';
				$html .= sprintf('<td>%s</td>', $field->Name);
				$html .= sprintf('<td>%s</td>', trim(str_replace('unsigned', '', $field->OriginalType)));
				$html .= sprintf('<td>%s</td>', implode('', $flagArray));
				if ($foreignKey) {
					if (array_key_exists($foreignKey->ReferenceTableName, $typeArrayByTableName)) {
						$html .= sprintf('<td class="types">%s</td>', implode('<br/>', $typeArrayByTableName[$foreignKey->ReferenceTableName]));
					} else {
						$html .= sprintf('<td>%s(%s)</td>', $foreignKey->ReferenceTableName, $foreignKey->ReferenceColumnNameArray[0]);
					}
				} else {
					$html .= '<td></td>';
				}
				$html .= '</tr>';
			}

			foreach ($database->GetIndexesForTable($tableName) as $index) {
				if ((count($index->ColumnNameArray) > 1) && !$index->PrimaryKey) {
					$html .= '<tr>';
					$html .= sprintf('<td>(%s)</td>', implode(', ', $index->ColumnNameArray));
					if ($index->Unique) {
						$html .= '<td>[unique]</td><td><span>U</span></td>';
					} else {
						$html .= '<td>[index]</td><td><span>i</span></td>';
					}
					$html .= '<td></td>';
					$html .= '</tr>';
				}
			}

			foreach ($tableArray as $reverseReferenceTableName) {
				$foreignKeyArray = $database->GetForeignKeysForTable($reverseReferenceTableName);
				foreach ($foreignKeyArray as $foreignKeyIndex => $foreignKey) {
					if ($foreignKey->ReferenceTableName == $tableName) {
						$assn = false;
						$unique = false;

						// Massage for ASSN tables
						if (substr($reverseReferenceTableName, strlen($reverseReferenceTableName) - 5) == '_assn') {
							$assn = $reverseReferenceTableName;
							$foreignKey = $foreignKeyArray[$foreignKeyIndex == 0 ? 1 : 0];
							$reverseReferenceTableName = $foreignKey->ReferenceTableName;
						// Check Unique?
						} else {
							foreach ($database->GetFieldsForTable($reverseReferenceTableName) as $field) {
								if ($field->Name == $foreignKey->ColumnNameArray[0]) {
									if ($field->Unique) $unique = true;
								}
							}
						}

						$html .= '<tr>';
						$html .= sprintf('<td>%s(%s)</td>', $reverseReferenceTableName, $foreignKey->ColumnNameArray[0]);
						if ($assn) {
							$html .= '<td>[reverse assn]</td>';
							$html .= '<td></td>';
							$html .= sprintf('<td>%s</td>', $assn);
						} else if ($unique) {
							$html .= '<td>[reverse unique fk]</td>';
							$html .= '<td></td>';
							$html .= '<td></td>';
						} else {
							$html .= '<td>[reverse fk]</td>';
							$html .= '<td></td>';
							$html .= '<td></td>';
						}
						$html .= '</tr>';
					}
				}
			}

			$html .= '</tbody></table>';

			$htmlArrayByTable[] = $html;
		}

		file_put_contents($path, implode("\n\n", $htmlArrayByTable));
	}

	private function Database_Schema(QDatabaseBase $database) {
		$tableArray = $database->GetTables();
		sort($tableArray);

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
				$foreignKey = self::GetForeignKey($databaseField->Name, $foreignKeyArray);
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
				$foreignKeyArray = $database->GetForeignKeysForTable($reverseReferenceTableName);
				foreach ($foreignKeyArray as $foreignKeyIndex => $foreignKey) {
					if ($foreignKey->ReferenceTableName == $tableName) {
						$as = null;

						// Massage for ASSN tables
						if (substr($reverseReferenceTableName, strlen($reverseReferenceTableName) - 5) == '_assn') {
							$foreignKey = $foreignKeyArray[$foreignKeyIndex == 0 ? 1 : 0];
							$reverseReferenceTableName = $foreignKey->ReferenceTableName;

						// Calculate "As" info (if applicable) for non-ASSN tables
						} else {
							$columnName = $foreignKey->ColumnNameArray[0];
							$columnName = str_replace($tableName . '_id', '', $columnName);
							if ($columnName) $as = 'As' . trim(QConvertNotation::CamelCaseFromUnderscore($columnName));
						}

						$propertyArray[] = sprintf('				"%s%s": {"type": "array", "items": { "$ref": "#/definitions/%s" }}',
							self::Pluralize(QConvertNotation::JavaCaseFromUnderscore($reverseReferenceTableName)),
							$as,
							QConvertNotation::CamelCaseFromUnderscore($reverseReferenceTableName));
					}
				}
			}

			$output .= implode(",\n", $propertyArray) . "\n";
			$output .= "			}
	}";

			print $output . "\n";
		}

	}
}
