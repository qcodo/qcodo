<?php

namespace Qcodo\Handlers\Console;
use Qcodo\Managers\CodegenSwagger;
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
	/**
	 * Used for Swagger-related rendering
	 * @var string[] indexed by schema name
	 */
	private $schemaDefinitionsToRender;
	/**
	 * Used for swagger-related rendering
	 * @var stdClass[] indexed by schema name
	 */
	private $schemaDefinitionArrayByName;

	/**
	 * Used for swagger-related rendering
	 * @var stdClass[] indexed by Tag
	 */
	private $methodDefinitionArrayByTag;

	public function Run() {
		$this->executeMethodList();
	}

	/**
	 * @param string $command should be codegen, csv, pdf or diff
	 * @param string $path the path to the swagger file (or if codegen, can also be path to swagger codegen settings json file)
	 * @param string $output if command is PDF, the path/folder to output the PDF to (required)
	 * @param string $size if command is PDF, the size of the paper (defaults to 'letter', but can be 'A4')
	 * @param string $originalPath if command is DIFF, this is required to be the 'original' swagger to diff against
	 * @return void
	 */
	public function Swagger($command, $path, $output = null, $size = 'letter', $originalPath = null) {
		$swagger = json_decode(file_get_contents($path));
		if (!$swagger) exit("invalid swagger: " . $path . "\n");

		if (!is_file($path)) exit("file not found: " . $path . "\n");
		$objectOrArray = json_decode(file_get_contents($path));
		if (!$objectOrArray) exit("invalid swagger or settings: " . $path . "\n");

		$swagger = null;
		$settings = null;

		if (is_object($objectOrArray) && isset($objectOrArray->swagger) && $objectOrArray->swagger) {
			$swagger = $objectOrArray;
		} else if (is_array($objectOrArray)) {
			$settings = $objectOrArray;
		} else {
			exit("uanble to process swagger or settings: " . $path . "\n");
		}

		switch (trim(strtolower($command))) {
			case 'codegen':
				if ($swagger) {
					$codegenSwaggerArray = array(new CodegenSwagger($swagger));
				} else {
					$codegenSwaggerArray = CodegenSwagger::CreateArrayFromSettings($settings, dirname($path));
				}
				foreach ($codegenSwaggerArray as $codegenSwagger) {
					print "Generating Schema for [" . $codegenSwagger->swagger->info->title . "]\n";
					$codegenSwagger->GenerateSchema();
					print "Generating Client for [" . $codegenSwagger->swagger->info->title . "]\n";
					$codegenSwagger->GenerateClient();

				}
				break;

			case 'csv':
				if (!$swagger) exit("csv requires a swagger file\n");
				$codegenSwagger = new CodegenSwagger($swagger);
				$rowArray = $codegenSwagger->GeneratePathReport();
				print QApplicationBase::generateCsvContent($rowArray);
				break;

			case 'pdf':
				if (!$swagger) exit("pdf requires a swagger file\n");
				if (!$output || !is_dir($output)) exit("path not found: " . $output . "\n");
				$this->Swagger_Pdf($swagger, $output . '/' . basename($path, '.json') . '.pdf', $size);
				break;

			case 'diff':
				if (!$swagger) exit("pdf requires a swagger file\n");
				if (!$originalPath || !is_file($originalPath)) exit("path not found: " . $originalPath . "\n");
				$this->Swagger_Diff($originalPath, $path);
				break;

			default:
				exit("unknown command: " . $command . "\n");
		}
	}

	private function TypeDefinition($type) {
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

	private function TypeDetails($typeDefinition, $requestOrResponse) {
		if (substr($typeDefinition, strlen($typeDefinition) - strlen($requestOrResponse)) == $requestOrResponse) {
			if (array_key_exists($typeDefinition, $this->schemaDefinitionsToRender)) $this->schemaDefinitionsToRender[$typeDefinition] = null;

			$schema = $this->schemaDefinitionArrayByName[$typeDefinition];

			$typeHtml = '';
			foreach ($schema->properties as $propertyName => $property) {
				$typeHtml .= sprintf('<div class="paramDefinition">&bull; %s <strong>$%s</strong></div>',
					self::TypeDefinition($property),
					$propertyName,
				);

				if (isset($property->enum) && is_array($property->enum)) {
					$typeHtml .= sprintf('<div class="paramEnum">%s</div>', implode(', ', $property->enum));
				}

				if (isset($property->description)) $typeHtml .= sprintf('<div class="paramDescription">%s</div>', htmlentities($property->description));
			}

			return $typeHtml;
		}

		return null;
	}

	private function Swagger_Diff($originalPath, $newPath) {
		$rowArray = array();
		$rowArray[] = array(
			'Method/Schema',
			'New/Change',
			'Name',
			'Method',
			'Path',
		);

		$result = $this->Swagger_Diff_Calculate($originalPath, $newPath);

		foreach ($result['methods'] as $method) {
			$rowArray[] = $row = array(
				'Method',
				ucfirst($method['type']),
				$method['operationId'],
				$method['method'],
				$method['path'],
			);
		}

		foreach ($result['schemas'] as $schema) {
			$rowArray[] = $row = array(
				'Schema',
				ucfirst($schema['type']),
				$schema['schema'],
			);
		}

		print QApplicationBase::generateCsvContent($rowArray) . "\n";
	}

	private function Swagger_Diff_Calculate($oldFile, $newFile) {
		$oldSwagger = json_decode(file_get_contents($oldFile), true);
		$newSwagger = json_decode(file_get_contents($newFile), true);

		if (!$oldSwagger || !$newSwagger) {
			die("Error: Unable to parse one or both of the Swagger files.\n");
		}

		$result = [
			'methods' => [],
			'schemas' => []
		];

		// Compare paths (methods)
		$oldPaths = $oldSwagger['paths'] ?? [];
		$newPaths = $newSwagger['paths'] ?? [];

		foreach ($newPaths as $path => $methods) {
			foreach ($methods as $method => $details) {
				$operationId = $details['operationId'] ?? null;
				if (!isset($oldPaths[$path][$method])) {
					$result['methods'][] = [
						'type' => 'new',
						'path' => $path,
						'operationId' => $operationId,
						'method' => $method,
					];
				} else if (json_encode($oldPaths[$path][$method]) !== json_encode($details)) {
					$result['methods'][] = [
						'type' => 'changed',
						'path' => $path,
						'operationId' => $operationId,
						'method' => $method
					];
				}
			}
		}

		// Compare components (schemas)
		$oldSchemas = $oldSwagger['definitions'] ?? [];
		$newSchemas = $newSwagger['definitions'] ?? [];

		foreach ($newSchemas as $schemaName => $schemaDetails) {
			if (!isset($oldSchemas[$schemaName])) {
				$result['schemas'][] = [
					'type' => 'new',
					'schema' => $schemaName
				];
			} else if (json_encode($oldSchemas[$schemaName]) !== json_encode($schemaDetails)) {
				$result['schemas'][] = [
					'type' => 'changed',
					'schema' => $schemaName
				];
			}
		}

		return $result;
	}

	private function Swagger_Pdf(stdClass $swagger, $outputFilePath, $size) {
		$this->methodDefinitionArrayByTag = array();
		foreach ($swagger->paths as $pathName => $path) {
			foreach ($path as $methodName => $definition) {
				$definition->pathName = $pathName;
				$definition->methodName = $methodName;
				foreach ($definition->tags as $tag) {
					if (!array_key_exists($tag, $this->methodDefinitionArrayByTag)) $this->methodDefinitionArrayByTag[$tag] = array();
					$this->methodDefinitionArrayByTag[$tag][] = $definition;
				}
			}
		}

		$this->schemaDefinitionArrayByName = array();
		$this->schemaDefinitionsToRender = array();
		foreach ($swagger->definitions as $name => $definition) {
			$this->schemaDefinitionArrayByName[$name] = $definition;
			$this->schemaDefinitionsToRender[$name] = $name;
		}

		$mpdf = new Mpdf\Mpdf([
			'tempDir' => '/tmp',
			'mode' => 'utf-8',
			'orientation' => 'P',
			'format' => $size,
			'margin_left' => 6,
			'margin_right' => 6,
			'margin_top' => 6,
			'margin_bottom' => 6,
			'margin_header' => 0,
			'margin_footer' => 3,
		]);
		$mpdf->setAutoTopMargin = 'stretch'; // Set pdf top margin to stretch to avoid content overlapping
		$mpdf->setAutoBottomMargin = 'false'; // Set pdf bottom margin to stretch to avoid content overlapping
		$mpdf->TOCpagebreakByArray(array(
			'links' => true,
			'toc-margin-left' => 10,
			'toc-margin-right' => 10,
			'toc-margin-top' => 10,
			'toc-margin-bottom' => 10,
		));
		$mpdf->h2toc = array(
			'H1' => 0,
			'H2' => 1,
			'H3' => 2
		);

		$this->Swagger_Pdf_ApiMethods($mpdf, $swagger);
		$this->Swagger_Pdf_SchemaDefinitions($mpdf, $swagger);

		// Set Footer for TOC
		$mpdf->AddPage();
		$replacementArray = array(
			'TITLE' => $swagger->info->title,
			'SUBTITLE' => 'Table of Contents',
		);
		$html = QApplicationBase::renderTemplateFromPath(dirname(__FILE__) . '/swagger-footer.html', $replacementArray);
		$mpdf->SetHTMLFooter($html);

		$mpdf->OutputFile($outputFilePath);
	}

	private function Swagger_Pdf_ApiMethods(Mpdf\Mpdf $mpdf, stdClass $swagger) {
		$mpdf->WriteHTML('<h1 style="font-weight: normal; padding: 0; margin: 0; color: #fff; font-size: 0pt;">API Methods</h1>');

		$tagArray = array_keys($this->methodDefinitionArrayByTag);
		sort($tagArray);

		$firstFlag = true;
		foreach ($tagArray as $tag) {
			$methodDefinitionArray = $this->methodDefinitionArrayByTag[$tag];
			$htmlArrayByTable = array();

			foreach ($methodDefinitionArray as $methodDefinitionIndex => $definition) {
				$parts = explode('::', $definition->operationId);

				$html = '<h3>' . $definition->operationId . '</h3>';
				$html .= '<div class="methodcontainer">';

				// Method - Title

				$html .= '<table class="title"><tbody>';
				$html .= sprintf('<tr><td><span class="operationClass">%s::</span><span class="operationName">%s</span></td><td class="endpoint">%s %s</td></tr>',
					$parts[0],
					$parts[1],
					strtoupper($definition->methodName),
					$definition->pathName);
				$html .= '</tbody></table>';

				// Method - Description

				$html .= sprintf('<div class="methoddescription">%s</div>', $definition->summary);

				// Method - Specs (Request and/or Response)

				$html .= '<table class="specs"><tbody>';

				$requestRendered = false;
				if ($definition->parameters && count($definition->parameters)) {
					$requestRendered = true;
					$html .= '<tr><td class="label">Request</td><td class="content">';

					$html .= '<table class="inputs"><thead><tr><th style="width: 35%;">Name / Type</th><th style="width: 65%;">Description</th></tr></thead><tbody>';
					foreach ($definition->parameters as $parameter) {
						$descriptionHtml = isset($parameter->description) ? '<em style="font-size: 7pt;">' . htmlentities($parameter->description) . '</em>' : '';

						switch ($parameter->in) {
							case 'body':
								$typeDefinition = $this->TypeDefinition(isset($parameter->type) ? $parameter : $parameter->schema);
								$typeDetailsHtml = $this->TypeDetails($typeDefinition, 'Request');

								$name = 'Body: ' . $typeDefinition;
								if ($typeDetailsHtml) {
									$name .= '<br/>' . $descriptionHtml;
									$descriptionHtml = $typeDetailsHtml;
								}
								break;
							default:
								$name = ucfirst($parameter->in) . ': ' . $parameter->name;
								break;
						}

						$html .= sprintf('<tr><td>%s</td><td>%s</td></tr>', $name, $descriptionHtml);
					}
					$html .= '</tbody></table>';

					$html .= '</td></tr>';
				}

				if ($definition->responses) {
					// Gutter
					if ($requestRendered) $html .= '<tr class="gutter"><td colspan="2"></td></tr>';

					$html .= '<tr><td class="label">Response</td><td class="content">';
					$html .= '<table class="inputs"><thead><tr><th style="width: 5%;">Code</th><th style="width: 95%;">Response</th></tr></thead><tbody>';
					foreach ($definition->responses as $statusCode => $response) {
						if (isset($response->schema)) {
							$typeDefinition = $this->TypeDefinition($response->schema);
							$typeDetailsHtml = $typeDefinition . '<br>' . $this->TypeDetails($typeDefinition, 'Response');
						} else {
							$typeDetailsHtml = '<div class="responseText">' . htmlentities($response->description) . '</div>';
						}

						$html .= sprintf('<tr><td>%s</td><td>%s</td></tr>', $statusCode, $typeDetailsHtml);
					}
					$html .= '</tbody></table>';

					$html .= '</td></tr>';
				}

				$html .= '</tbody></table>';

				$html .= '</div>';

				$htmlArrayByTable[] = $html;
			}


			// Set Footer for Tag
			$replacementArray = array(
				'TITLE' => $swagger->info->title,
				'SUBTITLE' => 'API: ' . $tag,
			);
			$html = QApplicationBase::renderTemplateFromPath(dirname(__FILE__) . '/swagger-footer.html', $replacementArray);
			$mpdf->SetHTMLFooter($html);

			// Write Content for Tag
			$replacementArray = array(
				'NAME' => $tag,
				'ITEMS' => implode("\n", $htmlArrayByTable)
			);

			if ($firstFlag) {
				$firstFlag = false;
				$replacementArray['FIRST'] = 'class="first"';
			}

			$html = QApplicationBase::renderTemplateFromPath(dirname(__FILE__) . '/swagger-api.html', $replacementArray);
			$mpdf->WriteHTML($html);
		}
	}

	private function Swagger_Pdf_SchemaDefinitions(Mpdf\Mpdf $mpdf, stdClass $swagger) {
		$mpdf->AddPage();
		$mpdf->WriteHTML('<h1 style="font-weight: normal; padding: 0; margin: 0; color: #fff; font-size: 0pt;">Schema Definitions</h1>');

		// Set Footer for Schema
		$replacementArray = array(
			'TITLE' => $swagger->info->title,
			'SUBTITLE' => 'Schema Definitions',
		);
		$html = QApplicationBase::renderTemplateFromPath(dirname(__FILE__) . '/swagger-footer.html', $replacementArray);
		$mpdf->SetHTMLFooter($html);

		sort($this->schemaDefinitionsToRender);

		$firstFlag = true;
		foreach ($this->schemaDefinitionsToRender as $schemaName) {
			if (!$schemaName) continue;
			$schemaDefinition = $this->schemaDefinitionArrayByName[$schemaName];

			// Write Content for Schema
			$rowArray = array();
			foreach ($schemaDefinition->properties as $name => $property) {
				$typeDefinition = $this->TypeDefinition($property);
				$rowArray[] = sprintf('<tr><td class="propertyName">%s</td><td class="propertyType">%s</td><td class="propertyDescription">%s</td></tr>',
					htmlentities($name),
					$typeDefinition,
					isset($property->description) ? htmlentities($property->description) : '');
			}

			$replacementArray = array(
				'NAME' => $schemaName,
				'ROWS' => implode("\n", $rowArray)
			);

			if ($firstFlag) {
				$firstFlag = false;
				$replacementArray['FIRST'] = 'class="first"';
			}

			$html = QApplicationBase::renderTemplateFromPath(dirname(__FILE__) . '/swagger-schema.html', $replacementArray);
			$mpdf->WriteHTML($html);
		}
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
