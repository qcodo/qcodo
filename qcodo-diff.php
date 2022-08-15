<?php

/**
 * Internal helper script to determine DIFFs between
 * a project's version of Qcodo and the Qcodo main repo
 */

if ($_SERVER['argc'] != 2) {
	exit("usage: php qcodo-diff.php PATH_TO_ROOT\n");
}

$rootPath = realpath($_SERVER['argv'][1]);
if (!is_dir($rootPath . '/vendor/qcodo/qcodo')) {
	exit("usage: php qcodo-diff.php PATH_TO_ROOT\n");
}

Recurse($rootPath . '/vendor/qcodo/qcodo', '');

function Recurse($rootPath, $currentDirectory) {
	$qcodoPath = dirname(__FILE__);

	$directory = opendir($rootPath . $currentDirectory);
	while ($filename = readdir($directory)) {

		// No Invisible Files
		if (substr($filename, 0, 1) == '.') continue;

		// Recurse Directory
		if (is_dir($rootPath . $currentDirectory . '/' . $filename)) {
			Recurse($rootPath, $currentDirectory . '/' . $filename);
			continue;
		}

		// File
		if (!file_exists($qcodoPath . $currentDirectory . '/' . $filename)) {
			printf("cp %s%s/%s %s%s\n", $rootPath, $currentDirectory, $filename, $qcodoPath, $currentDirectory);
		} else if (md5_file($rootPath . $currentDirectory . '/' . $filename) != md5_file($qcodoPath . $currentDirectory . '/' . $filename)) {
			printf("cp %s%s/%s %s%s\n", $rootPath, $currentDirectory, $filename, $qcodoPath, $currentDirectory);
		}
	}
}