<template OverwriteFlag="false" DocrootFlag="false" DirectorySuffix="" TargetDirectory="<%= __DATA_CLASSES__ %>" TargetFileName="<%= $objTable->ClassName %>.php"/>
<?php
	namespace <%= QApplicationBase::$application->rootNamespace %>\Models\Database;
	use <%= QApplicationBase::$application->rootNamespace %>\Managers\Application;

	require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'generated' . DIRECTORY_SEPARATOR . '<%= $objTable->ClassName %>Gen.php');

	/**
	 * The <%= $objTable->ClassName %> class defined here contains any
	 * customized code for the <%= $objTable->ClassName %> class in the
	 * Object Relational Model.  It represents the "<%= $objTable->Name %>" table 
	 * in the database, and extends from the code generated abstract <%= $objTable->ClassName %>Gen
	 * class, which contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 * 
	 * @package <%= QCodeGen::$ApplicationName; %>
	 * @subpackage DataObjects
	 * 
	 */
	class <%= $objTable->ClassName %> extends <%= $objTable->ClassName %>Gen {
		/**
		 * Default "to string" handler
		 * Allows pages to _p()/echo()/print() this object, and to define the default
		 * way this object would be outputted.
		 *
		 * Can also be called directly via $obj<%= $objTable->ClassName %>->__toString().
		 *
		 * @return string a nicely formatted string representation of this object
		 */
		public function __toString() {
			return sprintf('<%= $objTable->ClassName %> Object <% foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { %>%s - <% } %><%---%>', <% foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { %> $this-><%= $objColumn->VariableName %>, <% } %><%--%>);
		}


		<%@ example_load_methods('objTable'); %>



		<%@ example_properties('objTable'); %>
	}
