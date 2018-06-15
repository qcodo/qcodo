<template OverwriteFlag="true" DocrootFlag="false" DirectorySuffix="" TargetDirectory="<%= __DATAGEN_CLASSES__ %>" TargetFileName="QQN.class.php"/>
<?php
	class QQN {
<% foreach ($objTableArray as $objTable) { %>
		/**
		 * @return <%= QApplicationBase::$application->rootNamespace %>\Models\Database\QQNode<%= $objTable->ClassName %>
		 */
		static public function <%= $objTable->ClassName %>() {
			return new <%= QApplicationBase::$application->rootNamespace %>\Models\Database\QQNode<%= $objTable->ClassName %>('<%= $objTable->Name %>', null, null);
		}
<% } %>
	}
