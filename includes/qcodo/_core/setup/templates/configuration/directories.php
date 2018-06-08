<?php
	define('__ROOT__', %s);
	define('__VENDOR__', %s);
	define('__INCLUDES__', __VENDOR__ . DIRECTORY_SEPARATOR . 'qcodo' . DIRECTORY_SEPARATOR . 'qcodo'. DIRECTORY_SEPARATOR . 'includes');

	define ('__QCODO__', __INCLUDES__ . '/qcodo');
	define ('__QCODO_CORE__', __INCLUDES__ . '/qcodo/_core');

	define('__ERROR_LOG__', __ROOT__ . '/../error_log');
	define('__QCODO_LOG__', __ROOT__ . '/../log');
