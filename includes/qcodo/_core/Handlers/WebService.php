<?php

namespace Qcodo\Handlers;
use Qcodo\Utilities\Swagger;
use Qcodo\Utilities\HttpRequest;
use Qcodo\Utilities\HttpResponse;
use QApplicationBase;
use Exception;

abstract class WebService extends Base {
	const ConfigurationNamespace = '.ws';

	/**
	 * @var $request HttpRequest
	 */
	protected $request;

	public static function Run(Swagger $swagger, $settings) {
		$request = new HttpRequest();

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
		} else {
			// No -- we are making a mock
			$content = $swagger->getExampleAtPathAndMethod($foundPath, $request->method);

			if (($content instanceof \stdClass) || is_array($content)) $content = json_encode($content);
			$response = new HttpResponse(200, $content);
		}

		$response->execute();
	}

	public function __construct(HttpRequest $request) {
		$this->request = $request;
	}
}