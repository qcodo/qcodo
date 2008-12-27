Please note: this file is deprecated as of 0.3.33

If you wish to run Qcodo without any QForm interactions, simply comment out the following lines in qcodo.inc.php:

	QApplicationBase::$PreloadedClassFile['_enumerations'] = __QCODO_CORE__ . '/qform/_enumerations.inc.php';
	QApplicationBase::$PreloadedClassFile['QControlBase'] = __QCODO_CORE__ . '/qform/QControlBase.class.php';
	QApplicationBase::$PreloadedClassFile['QControl'] = __QCODO__ . '/qform/QControl.class.php';
	QApplicationBase::$PreloadedClassFile['QFormBase'] = __QCODO_CORE__ . '/qform/QFormBase.class.php';
	QApplicationBase::$PreloadedClassFile['QForm'] = __QCODO__ . '/qform/QForm.class.php';
	QApplicationBase::$PreloadedClassFile['_actions'] = __QCODO_CORE__ . '/qform/_actions.inc.php';
	QApplicationBase::$PreloadedClassFile['_events'] = __QCODO_CORE__ . '/qform/_events.inc.php';

With those lines commented out, nothing QForm-related will ever get loaded into your application.