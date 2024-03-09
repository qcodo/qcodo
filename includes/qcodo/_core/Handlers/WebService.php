<?php

namespace Qcodo\Handlers;
use Qcodo\Utilities\Swagger;
use Qcodo\Utilities\HttpRequest;
use Qcodo\Utilities\HttpResponse;
use QApplicationBase;
use Exception;
use stdClass;
use QLog;
use QLogLevel;

abstract class WebService extends Base {
	const ConfigurationNamespace = '.ws';

	/**
	 * whether to log all raw request/responses to the qcodo log
	 * @var boolean|string $LogFlag
	 */
	public static $LogFlag = false;
	public static $LogLevel = QLogLevel::Normal;
	public static $LogModule = 'default';

	/**
	 * Singleton-style access to the HttpRequest that is being operated on, for example, for ErrorLogging purposes
	 * @var HttpRequest $HttpRequest
	 */
	public static $HttpRequest;

	/**
	 * @var HttpRequest $request
	 */
	protected $request;

	/**
	 * @var string $operationId
	 */
	public $operationId;

	private static function RunErrorIndex($viewErrorLogCommand) {
		$template = file_get_contents(dirname(__FILE__) . '/ErrorPageTemplate.html');

		$viewer = new \QErrorLogViewer(__ERROR_LOG__);
		$errorXmlArray = $viewer->GetAsDataSource();

		$body = '';

		foreach ($errorXmlArray as $errorXml) {
			$date = new \QDateTime($errorXml->isoDateTime);
			$type = (htmlentities((string) $errorXml->type)) ?? 'Other';
			$title = htmlentities($errorXml->title);
			$serverAndScript = sprintf('<strong>%s</strong><br/><span class="meta">%s</span>',
				htmlentities($errorXml->script),
				htmlentities($errorXml->server)
			);
			$agent = sprintf('<span class="meta">%s</span>',
				htmlentities($errorXml->agent)
			);

			$body .= '<tr>';
			$body .= sprintf('<td><a href="%s/view/%s">View</a></td>', $viewErrorLogCommand, $errorXml->filename);
			$body .= sprintf('<td><a href="%s/delete/%s">Delete</a></td>', $viewErrorLogCommand, $errorXml->filename);
			$body .= sprintf('<td>%s</td>', $date);
			$body .= sprintf('<td>%s</td>', $type);
			$body .= sprintf('<td>%s</td>', $title);
			$body .= sprintf('<td>%s</td>', $serverAndScript);
			$body .= sprintf('<td>%s</td>', $agent);
			$body .= '</tr>';
		}

		print (str_replace('%BODY%', $body, $template));
	}

	private static function RunErrorView($viewErrorLogCommand, $errorLogFile) {
		$path = __ERROR_LOG__ . '/' . $errorLogFile;
		if (is_file($path)) {
			readfile($path);
			return true;
		}
		return false;
	}

	private static function RunErrorDelete($viewErrorLogCommand, $errorLogFile) {
		$path = __ERROR_LOG__ . '/' . $errorLogFile;
		if (is_file($path)) {
			@unlink($path);
			header('Location: ' . $viewErrorLogCommand);
			return true;
		}
		return false;
	}

	private static function emlToJson($emlFilePath) {
		// Read .eml file
		$emlContent = file_get_contents($emlFilePath);

		// Parse .eml content
		$emlParts = preg_split("/\r?\n\r?\n/", $emlContent, 2);
		$headers = $emlParts[0];
		$body = $emlParts[1] ?? '';

		// Parse headers into associative array
		$headerLines = explode("\n", $headers);
		$headerData = [];
		foreach ($headerLines as $headerLine) {
			if (strpos($headerLine, ':') !== false) {
				list($key, $value) = explode(':', $headerLine, 2);
				$headerData[trim($key)] = trim($value);
			}
		}

		// Return parsed data as JSON
		return json_encode([
			'headers' => $headerData,
			'body' => $body
		]);
	}

	private static function RunFailedEmailLog($viewEmailLogCommand, HttpRequest $request, $failedEmailLogRelativePath) {
		$template = file_get_contents(dirname(__FILE__) . '/FailedEmailLogPageTemplate.html');

		$array = array();

		$directory = opendir($failedEmailLogRelativePath);
		while ($file = readdir($directory)) {
			if (strpos($file, '.eml')) {
				$json = self::emlToJson($failedEmailLogRelativePath . '/' . $file);
				$object = json_decode($json);
				$object->Date = new \QDateTime($object->headers->Date);
				$array[] = $object;
			}
		}

		usort($array, [self::class, 'RunFailedEmailLog_Sort']);

		$trArray = array();
		foreach ($array as $object) {
			$tr = '<tr>';
			$tr .= sprintf('<td>%s</td>', $object->Date->ToString('YYYY-MM-DD hhhh:mm:ss'));
			$tr .= sprintf('<td>%s</td>', htmlentities($object->headers->From));
			$tr .= sprintf('<td>%s</td>', htmlentities($object->headers->To));
			$tr .= sprintf('<td>%s</td>', htmlentities($object->headers->Subject));
			$tr .= '</tr>';
			$trArray[] = $tr;
		}
		print (str_replace('%BODY%', implode("\n", $trArray), $template));

	}

	private static function RunFailedEmailLog_Sort(stdClass $a, stdClass $b) {
		if ($a->Date->IsEqualTo($b->Date)) return 0;
		return ($a->Date->IsEarlierThan($b->Date)) ? -1 : 1;
	}

	private static function RunError($viewErrorLogCommand, HttpRequest $request) {
		// Error Log Authentication (if applicable)
		$viewErrorLogAuthentication = QApplicationBase::$application->getConfiguration(self::ConfigurationNamespace, 'viewErrorLogAuthentication');
		if ($viewErrorLogAuthentication) {
			if (!is_array($viewErrorLogAuthentication)) throw new Exception('error log authentication is non-array');
			if (count($viewErrorLogAuthentication) != 2) throw new Exception('error log authentication is malformed');
			$username = $viewErrorLogAuthentication[0];
			$password = $viewErrorLogAuthentication[1];

			if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
				($_SERVER['PHP_AUTH_USER'] != $username) || ($_SERVER['PHP_AUTH_PW'] != $password)) {
				header('WWW-Authenticate: Basic realm="Qcodo Error Viewer"');
				header('HTTP/1.0 401 Unauthorized');
				print('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>401 Unauthorized</title>
</head><body>
<h1>Unauthorized</h1>
<p>You are not authorized to view this page.</p>
</body></html>');
				exit;
			}
		}

		$path = substr($request->path, strlen($viewErrorLogCommand));

		if (!$path) {
			self::RunErrorIndex($viewErrorLogCommand);
			return;
		}

		$parts = explode('/', $path);
		switch ($parts[1]) {
			case 'view':
				if (self::RunErrorView($viewErrorLogCommand, $parts[2])) return;
				break;

			case 'delete':
				if (self::RunErrorDelete($viewErrorLogCommand, $parts[2])) return;
				break;
		}
	}

	public static function Run(Swagger $swagger) {
		$request = new HttpRequest();

		// CORS Pre-Flight
		if ($request->method == "OPTIONS") {
			$response = new HttpResponse(200, 'OK');

			if (array_key_exists('Origin', $request->headersArray))
				$response->setHeader('Access-Control-Allow-Origin', $request->headersArray['Origin']);

			$response->execute();
			return;
		}

		// Are we explicitly asking to view the error log?
		$viewErrorLogCommand = QApplicationBase::$application->getConfiguration(self::ConfigurationNamespace, 'viewErrorLogCommand');
		if ($viewErrorLogCommand && (strpos($request->path, $viewErrorLogCommand) === 0)) {
			return self::RunError($viewErrorLogCommand, $request);
		}

		// Are we explicitly asking to view the swagger spec?
		$viewSpecificationCommand = QApplicationBase::$application->getConfiguration(self::ConfigurationNamespace, 'viewSpecificationCommand');
		if ($viewSpecificationCommand && ($viewSpecificationCommand == $request->path)) {
			$response = new HttpResponse(200, $swagger->getOriginalJson(), 'application/json');
			$response->execute();
			return;
		}

		// Are we explicitly asking to view the failed email logz?
		$viewEmailLogCommand = QApplicationBase::$application->getConfiguration(self::ConfigurationNamespace, 'viewFailedEmailLogCommand');
		$failedEmailLogRelativePath = QApplicationBase::$application->getConfiguration(self::ConfigurationNamespace, 'failedEmailLogRelativePath');
		if ($viewEmailLogCommand && (strpos($request->path, $viewEmailLogCommand) === 0) && $failedEmailLogRelativePath) {
			return self::RunFailedEmailLog($viewEmailLogCommand, $request, $failedEmailLogRelativePath);
		}

		// Is there no found path in the Swagger?
		$foundPath = $swagger->getNamedPathForRequestPath($request->path);
		if (!$foundPath) {
			$response = new HttpResponse(405, 'No ' . $request->method . ' method at Path: ' . $request->path);
			$response->execute();
			return;
		}

		// Calculate Routing to the appropriate Handler
		$request->setPathParametersArrayForNamedPath($foundPath);

		try {
			$operationInfo = $swagger->getOperationForPathAndMethod($foundPath, $request->method);
		} catch (Exception $exception) {
			if ($exception->getMessage() == 'Method Not Defined') {
				$response = new HttpResponse(405, 'No ' . $request->method . ' method at Path: ' . $request->path);
				$response->execute();
				return;
			}

			throw $exception;
		}

		$className = $operationInfo[0];
		$methodName = $operationInfo[1];
		$fullyQualifiedClassName = QApplicationBase::$application->rootNamespace . '\\Handlers\\WebService\\' . $className;

		// Does the class and method exist?
		if (class_exists($fullyQualifiedClassName) && method_exists($fullyQualifiedClassName, $methodName)) {
			// Yes -- we are making the call
			$apiHandlerObject = new $fullyQualifiedClassName($request, implode('::', $operationInfo));
			$response = $apiHandlerObject->$methodName();
			if (!$response) $response = new HttpResponse(500, 'No HttpResponse when calling ' . $className . '::' . $methodName);
			if (!($response instanceof HttpResponse)) {
				$responseClassName = get_class($response);
				$response = new HttpResponse(500, 'Not a valid HttpResponse when calling ' . $className . '::' . $methodName . ' - a ' . $responseClassName . ' was returned');
			}
		} else {
			// No -- we are making a mock
			$content = $swagger->getExampleAtPathAndMethod($foundPath, $request->method);

			if (($content instanceof stdClass) || is_array($content)) $content = json_encode($content);
			$response = new HttpResponse(200, $content);
		}

		if (self::$LogFlag) {
			QLog::Log(sprintf('%s %s', self::$HttpRequest->method, self::$HttpRequest->httpUri), self::$LogLevel, self::$LogModule);
			QLog::LogObject(self::$HttpRequest, self::$LogLevel, self::$LogModule);
			QLog::LogObject($response, self::$LogLevel, self::$LogModule);
		}

		$response->execute();
	}

	public function __construct(HttpRequest $request, $operationId) {
		$this->request = $request;
		$this->operationId = $operationId;
		self::$HttpRequest = $request;
	}
}
