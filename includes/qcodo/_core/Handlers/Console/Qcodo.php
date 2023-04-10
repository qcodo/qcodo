<?php

namespace Qcodo\Handlers\Console;
use \Exception;
use Qcodo\Handlers;
use \QApplicationBase;
use \QDatabaseBase;
use \QDatabaseFieldType;
use \QString;
use \QConvertNotation;
use Mpdf;
use \stdClass;

class Qcodo extends Handlers\Console {
	public function Run() {
		$this->executeMethodList();
	}

	/**
	 * @param string $path the path to the swagger file to generate
	 * @param string $command should be codegen, csv or pdf
	 * @param string $output if command is PDF, the path/folder to output the PDF to
	 * @return void
	 */
	public function Swagger($path, $command, $output = null) {
		$swagger = json_decode(file_get_contents($path));
		if (!$swagger) exit("invalid swagger: " . $path . "\n");

		switch (trim(strtolower($command))) {
			case 'codegen':
				exit("not yet implemented\n");

			case 'csv':
				$this->Swagger_Csv($swagger);
				break;

			case 'pdf':
				if (!$output || !is_dir($output)) exit("path not found: " . $output . "\n");
				$this->Swagger_Pdf($swagger, $output . '/' . basename($path, '.json') . '.pdf');
				break;
		}
	}

	private static function TypeDefinition($type) {
		$ref = '$ref';

		if (isset($type->type)) {
			if (isset($type->format)) return sprintf('%s (%s)', $type->type, $type->format);
			if ($type->type == 'array') return self::TypeDefinition($type->items) . '[]';
			return $type->type;
		} else if (isset($type->$ref)) {
			return str_replace('#/definitions/', '', $type->$ref);
		}
		return '?';
	}

	private static function TypeHtml($typeDefinition, $requestOrResponse, &$schemaDefinitionsToRender, $schemaDefinitionArrayByName) {
		if (substr($typeDefinition, strlen($typeDefinition) - strlen($requestOrResponse)) == $requestOrResponse) {
			if (array_key_exists($typeDefinition, $schemaDefinitionsToRender)) unset($schemaDefinitionsToRender[$typeDefinition]);

			$schema = $schemaDefinitionArrayByName[$typeDefinition];

			$typeHtml = $typeDefinition . '<ul>';
			foreach ($schema->properties as $propertyName => $property) {
				$typeHtml .= sprintf('<li>%s <strong>$%s</strong>%s<em>%s</em></li>',
					self::TypeDefinition($property),
					$propertyName,
					isset($property->description) ? '<br/>' : '',
					isset($property->description) ? $property->description : '',
				);
			}
			$typeHtml .= '</ul>';
		} else {
			$typeHtml = $typeDefinition;
		}

		return $typeHtml;
	}

	private function Swagger_Pdf(stdClass $swagger, $outputFilePath) {
		$methodDefinitionArrayByTag = array();
		foreach ($swagger->paths as $pathName => $path) {
			foreach ($path as $methodName => $definition) {
				$definition->pathName = $pathName;
				$definition->methodName = $methodName;
				foreach ($definition->tags as $tag) {
					if (!array_key_exists($tag, $methodDefinitionArrayByTag)) $methodDefinitionArrayByTag[$tag] = array();
					$methodDefinitionArrayByTag[$tag][] = $definition;
				}
			}
		}

		$schemaDefinitionArrayByName = array();
		$schemaDefinitionsToRender = array();
		foreach ($swagger->definitions as $name => $definition) {
			$schemaDefinitionArrayByName[$name] = $definition;
			$schemaDefinitionsToRender[$name] = $name;
		}

		$htmlArrayByTable = array();
		foreach ($methodDefinitionArrayByTag as $tag => $methodDefinitionArray) {
			$html = '<div class="tag">';
			$html .= '<h3>' . $tag . '</h3>';

			foreach ($methodDefinitionArray as $definition) {
				$parts = explode('::', $definition->operationId);

				$html .= '<div class="methodcontainer">';
				$html .= '<table class="title"><tbody>';
				$html .= sprintf('<tr><td><span class="operationClass">%s::</span><span class="operationName">%s</span></td><td class="endpoint">%s %s</td></tr>',
					$parts[0],
					$parts[1],
					strtoupper($definition->methodName),
					$definition->pathName);
				$html .= '</tbody></table>';

				$html .= sprintf('<div class="methoddescription">%s</div>', $definition->summary);

				if ($definition->parameters && count($definition->parameters)) {
					$html .= '<table class="specs"><tbody><tr><td class="label">Input(s)</td><td class="content">';

					$html .= '<table class="inputs"><thead><tr><th style="width: 25%;">Name</th><th style="width: 25%;">Description</th><th style="width: 50%;">Type</th></tr></thead><tbody>';
					foreach ($definition->parameters as $parameter) {
						if (isset($parameter->type)) {
							$typeDefinition = self::TypeDefinition($parameter);
						} else {
							$typeDefinition = self::TypeDefinition($parameter->schema);
						}

						$typeHtml = self::TypeHtml($typeDefinition, 'Request', $schemaDefinitionsToRender, $schemaDefinitionArrayByName);

						$html .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', $parameter->name, isset($parameter->description) ? $parameter->description : '', $typeHtml);
					}
					$html .= '</tbody></table>';

					$html .= '</td></tbody></table>';
				}

				if ($definition->responses) {
					$html .= '<table class="specs"><tbody><tr><td class="label">Output(s)</td><td class="content">';
					$html .= '<table class="inputs"><thead><tr><th style="width: 15%;">Code</th><th style="width: 35%;">Description</th><th style="width: 50%;">Type</th></tr></thead><tbody>';
					foreach ($definition->responses as $statusCode => $response) {
						if (isset($response->schema)) {
							$typeDefinition = self::TypeDefinition($response->schema);
							$typeHtml = self::TypeHtml($typeDefinition, 'Response', $schemaDefinitionsToRender, $schemaDefinitionArrayByName);
						} else {
							$typeHtml = '';
						}

						$html .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', $statusCode, isset($response->description) ? $response->description : '', $typeHtml);
					}
					$html .= '</tbody></table>';

					$html .= '</td></tbody></table>';
				}

				$html .= '</div>';
			}

			$html .= '</div>';

			$htmlArrayByTable[] = $html;
		}

		$htmlTop = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		table { border: 0; border-collapse: collapse; width: 100%; }

        div.page { box-sizing: border-box; padding: 0.25in; }

		div.tag { margin-bottom: 24pt; }

		div.methodcontainer { margin-left: 20pt; margin-bottom: 24px;}
		h3 { color: #278; padding: 0; margin: 0 0 2pt 0; }

		table.title tr td { border-top: 3px solid #eee; border-bottom: 1px solid #eee; padding: 2pt 0 2pt 0; }
		table.title tr td.endpoint { text-align: right; font-family: monospace; }
		div.methoddescription { font-style: italic; padding-left: 20pt; padding-top: 2pt; }

		table.specs { margin-top: 12pt; }
		table.specs td.label { font-weight: bold; text-align: right; width: 20%; vertical-align: top; border-right: 1px solid #eee; padding-right: 12pt; }
		table.specs td.content { width: 80%; vertical-align: top; padding-left: 12pt; }

		table.inputs { }
		table.inputs thead tr th { text-align: left; color: #000; font-size: 8pt; font-weight: bold; }
		table.inputs tbody tr td { vertical-align: top;}
		table.inputs tbody tr:nth-child(odd) {background-color: #eee; }

		table.inputs tbody tr td ul { padding: 0; padding-left: 9pt; margin: 0; font-size: 9pt; }
		table.inputs tbody tr td li { padding: 0; margin: 0; }

		table tbody tr.method td { border-top: 1px solid #eee; }
		table tbody tr.description td { 
		table tbody tr.spec td.label { text-align: right; font-weight: bold; padding-right: 12pt;}

		span.operationClass { color: #999; }
		span.operationName { font-weight: bold; }
		
	</style>
</head>
<body style="margin: 0; padding: 0; width: 11.0in; height: 8.5in; font-family: arial, serif; font-size: 10pt; ">
<div class="page">';

		$htmlBottom = '</div></body></html>';

		$html = $htmlTop . implode("\n", $htmlArrayByTable) . $htmlBottom;

//		file_put_contents($outputFilePath, $html); exit();

		$mpdf = new Mpdf\Mpdf([
			'tempDir' => '/tmp',
			'mode' => 'utf-8',
			'orientation' => 'P',
			'format' => 'A4',
			'margin_left' => 0,
			'margin_right' => 0,
			'margin_top' => 0,
			'margin_bottom' => 0,
			'margin_header' => 0,
			'margin_footer' => 0,
		]);
		$mpdf->setAutoTopMargin = 'stretch'; // Set pdf top margin to stretch to avoid content overlapping
		$mpdf->setAutoBottomMargin = 'false'; // Set pdf bottom margin to stretch to avoid content overlapping

		$mpdf->WriteHTML($html);
		$mpdf->OutputFile($outputFilePath);
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
				$this->Database_Pdf($database, $output . '/' . $index . '.pdf');
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

			$html = '<div class="item">';
			$html .= '<h3>' . $tableName . '</h3>';
			$html .= '<div class="tablecontainer">';
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
				if ($field->Unique) $flagArray[] = '<span class="unique">U</span>';
				if ($field->Indexed) $flagArray[] = '<span class="indexed">i</span>';
				if ($field->Unsigned) $flagArray[] = '<span class="unsigned">&plusmn;</span>';
				if ($field->Identity) $flagArray[] = '<span class="autoinc">A</span>';
				if ($field->NotNull) $flagArray[] = '<span class="notnull">N</span>';
				if ($field->PrimaryKey) $flagArray[] = '<span class="primary">P</span>';
				if ($foreignKey) $flagArray[] = '<span class="foreign">F</span>';

				$html .= '<tr>';
				$html .= sprintf('<td>%s</td>', $field->Name);
				$html .= sprintf('<td>%s</td>', trim(str_replace('unsigned', '', $field->OriginalType)));
				$html .= sprintf('<td class="flags">%s</td>', implode(' ', $flagArray));
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
					$html .= '<tr class="index">';
					$html .= sprintf('<td>(%s)</td>', implode(', ', $index->ColumnNameArray));
					if ($index->Unique) {
						$html .= '<td>[unique]</td><td class="flags"><span>U</span></td>';
					} else {
						$html .= '<td>[index]</td><td class="flags"><span>i</span></td>';
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

						$html .= '<tr class="fk">';
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

			$html .= '</tbody></table></div></div>';

			$htmlArrayByTable[] = $html;
		}

		$htmlTop = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
        div.page { box-sizing: border-box; padding: 0.25in; }

		div.item { margin-bottom: 12pt; }

		div.tablecontainer { margin-left: 20pt; }

		h3 { color: #278; padding: 0; margin: 0 0 2pt 0; }
		table { border: 0; border-collapse: collapse; width: 100%; }
		table thead tr th { text-align: left; background-color: #000; color: #fff; padding: 2pt 0; }
		table thead tr th:first-child {padding-left: 5pt;}
		table tbody tr td { border-bottom: 1px solid #eee; vertical-align: top;}
		table tbody tr td.types { font-family: monospace; font-size: 7pt; }

		table tbody tr td.flags { }
		table tbody tr td.flags span { font-weight: bold }
		table tbody tr td.flags span.autoinc { color: #660; }
		table tbody tr td.flags span.unique { color: #060; }
		table tbody tr td.flags span.indexed { color: #060; }
		table tbody tr td.flags span.unsigned { color: #444; }
		table tbody tr td.flags span.notnull { color: #600; }
		table tbody tr td.flags span.primary { color: #226; }
		table tbody tr td.flags span.foreign { color: #626; }

		table tbody tr.index td { background-color: #dde; border-color: #fff; font-size: 8pt;}
		table tbody tr.index td:first-child {padding-left: 5pt;}
		table tbody tr.fk td { background-color: #eee; border-color: #fff; font-size: 8pt;}
		table tbody tr.fk td { font-style: italic; }
		table tbody tr.fk td:first-child {padding-left: 5pt;}
	</style>
</head>
<body style="margin: 0; padding: 0; width: 11.0in; height: 8.5in; font-family: arial, serif; font-size: 10pt; ">
<div class="page">';

		$htmlBottom = '</div></body></html>';

		$html = $htmlTop . implode("\n", $htmlArrayByTable) . $htmlBottom;

		$mpdf = new Mpdf\Mpdf([
			'tempDir' => '/tmp',
			'mode' => 'utf-8',
			'orientation' => 'P',
			'format' => 'A4',
			'margin_left' => 0,
			'margin_right' => 0,
			'margin_top' => 0,
			'margin_bottom' => 0,
			'margin_header' => 0,
			'margin_footer' => 0,
		]);
		$mpdf->setAutoTopMargin = 'stretch'; // Set pdf top margin to stretch to avoid content overlapping
		$mpdf->setAutoBottomMargin = 'false'; // Set pdf bottom margin to stretch to avoid content overlapping

		$mpdf->WriteHTML($html);
		$mpdf->OutputFile($path);
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
