<?php
	use Composer\Script\Event;
	use Composer\Installer\PackageEvent;

	class QInstaller extends QBaseClass {
		public static function postPackageInstall(PackageEvent $event) {
			var_dump($vent);
	        $installedPackage = $event->getOperation()->getPackage();
			var_dump($installedPackage);
		}
	}
