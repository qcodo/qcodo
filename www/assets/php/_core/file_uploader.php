<?php
	require(dirname(__FILE__) . '/../_require_prepend.inc.php');
	$strApcUploadKey = QApplication::PathInfo(0);

	if (!function_exists('apc_fetch')) exit();

	$strUploadData = apc_fetch('upload_' . $strApcUploadKey);
	if (!$strUploadData) exit();

	$fltPercent = floor(1000 * $strUploadData['current'] / $strUploadData['total']);
	$fltPercent = sprintf('%.1f%%', $fltPercent / 10);
	$intPercentFloor = floor($fltPercent);

	header('Content-Type: text/xml');
?>
<uploadData
	key="<?php _p($strApcUploadKey);?>"
	total="<?php _p(QString::GetByteSize($strUploadData['total'])); ?>"
	current="<?php _p(QString::GetByteSize($strUploadData['current'])); ?>"
	percent="<?php _p($fltPercent); ?>"
	percentFloor="<?php _p($intPercentFloor); ?>" />