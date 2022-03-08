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
	 * @var HttpRequest
	 */
	public static $HttpRequest;

	/**
	 * @var $request HttpRequest
	 */
	protected $request;

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

	public static function Run(Swagger $swagger, $settings) {
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

		// Are we explicitly asking to view the swagger spec?
		$viewSpecificationCommand = QApplicationBase::$application->getConfiguration(self::ConfigurationNamespace, 'viewSpecificationCommand');
		if ($viewSpecificationCommand && ($viewSpecificationCommand == $request->path)) {
			$response = new HttpResponse(200, $swagger->getOriginalJson(), 'application/json');
			$response->execute();
			return;
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
			$apiHandlerObject = new $fullyQualifiedClassName($request);
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

	public function __construct(HttpRequest $request) {
		$this->request = $request;
		self::$HttpRequest = $request;
	}
}
