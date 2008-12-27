<?php
	// This is the "Pro" version of configuration.inc.php, without any comments, and restructured in a way
	// that should make sense for most pro-users of Qcodo
	
	// As always, feel free to use, change or ignore.

	define('SERVER_INSTANCE', 'dev');

	switch (SERVER_INSTANCE) {
		case 'dev':
		case 'test':
		case 'stage':
		case 'prod':
			define ('__DOCROOT__', '/home/qcodo/wwwroot');
			define ('__VIRTUAL_DIRECTORY__', '');
			define ('__SUBDIRECTORY__', '');

			define('DB_CONNECTION_1', serialize(array(
				'adapter' => 'MySqli5',
				'server' => 'localhost',
				'port' => null,
				'database' => 'test',
				'username' => 'root',
				'password' => '',
				'profiling' => false)));
			break;
	}

	define('ALLOW_REMOTE_ADMIN', false);
	define ('__URL_REWRITE__', 'none');

	define ('__DEVTOOLS_CLI__', __DOCROOT__ . __SUBDIRECTORY__ . '/../_devtools_cli');
	define ('__INCLUDES__', __DOCROOT__ .  __SUBDIRECTORY__ . '/includes');
	define ('__QCODO__', __INCLUDES__ . '/qcodo');
	define ('__QCODO_CORE__', __INCLUDES__ . '/qcodo/_core');
	define ('__DATA_CLASSES__', __INCLUDES__ . '/data_classes');
	define ('__DATAGEN_CLASSES__', __INCLUDES__ . '/data_classes/generated');
	define ('__DATA_META_CONTROLS__', __INCLUDES__ . '/data_meta_controls');
	define ('__DATAGEN_META_CONTROLS__', __INCLUDES__ . '/data_meta_controls/generated');

	define ('__DEVTOOLS__', __SUBDIRECTORY__ . '/_devtools');
	define ('__FORM_DRAFTS__', __SUBDIRECTORY__ . '/drafts');
	define ('__PANEL_DRAFTS__', __SUBDIRECTORY__ . '/drafts/dashboard');

	// We don't want "Examples", and we don't want to download them during qcodo_update
	define ('__EXAMPLES__', null);

	define ('__JS_ASSETS__', __SUBDIRECTORY__ . '/assets/js');
	define ('__CSS_ASSETS__', __SUBDIRECTORY__ . '/assets/css');
	define ('__IMAGE_ASSETS__', __SUBDIRECTORY__ . '/assets/images');
	define ('__PHP_ASSETS__', __SUBDIRECTORY__ . '/assets/php');

	if ((function_exists('date_default_timezone_set')) && (!ini_get('date.timezone')))
		date_default_timezone_set('America/Los_Angeles');

	define('ERROR_PAGE_PATH', __PHP_ASSETS__ . '/_core/error_page.php');
//	define('ERROR_LOG_PATH', __INCLUDES__ . '/error_log');

//	define('ERROR_FRIENDLY_PAGE_PATH', __PHP_ASSETS__ . '/friendly_error_page.php');
//	define('ERROR_FRIENDLY_AJAX_MESSAGE', 'Oops!  An error has occurred.\r\n\r\nThe error was logged, and we will take a look into this right away.');
?>