<template OverwriteFlag="true" DocrootFlag="false" DirectorySuffix="" TargetDirectory="<%= __DATAGEN_CLASSES__ %>" TargetFileName="<%= $objTypeTable->ClassName %>Gen.php"/>
<?php
	namespace <%= QApplicationBase::$application->rootNamespace %>\Models\Database;
	use QBaseClass;
	use QCallerException;

	/**
	 * The <%= $objTypeTable->ClassName %> class defined here contains
	 * code for the <%= $objTypeTable->ClassName %> enumerated type.  It represents
	 * the enumerated values found in the "<%= $objTypeTable->Name %>" table
	 * in the database.
	 * 
	 * To use, you should use the <%= $objTypeTable->ClassName %> subclass which
	 * extends this <%= $objTypeTable->ClassName %>Gen class.
	 * 
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the <%= $objTypeTable->ClassName %> class.
	 * 
	 * @package <%= QCodeGen::$ApplicationName; %>
	 * @subpackage GeneratedDataObjects
	 */
	abstract class <%= $objTypeTable->ClassName %>Gen extends QBaseClass {
<%= ($intKey = 0) == 1; %><% foreach ($objTypeTable->TokenArray as $intKey=>$strValue) { %>
		const <%= $strValue %> = <%= $intKey %>;
<% } %>

		const MaxId = <%= $intKey %>;

		public static $NameArray = array(<% if (count($objTypeTable->NameArray)) { %>

<% foreach ($objTypeTable->NameArray as $intKey=>$strValue) { %>
			<%= $intKey %> => '<%= $strValue %>',
<% } %><%--%><%}%>);

		public static $TokenArray = array(<% if (count($objTypeTable->TokenArray)) { %>

<% foreach ($objTypeTable->TokenArray as $intKey=>$strValue) { %>
			<%= $intKey %> => '<%= $strValue %>',
<% } %><%--%><%}%>);

<% if (count($objTypeTable->ExtraFieldNamesArray)) { %>
		public static $ExtraColumnNamesArray = array(
<% foreach ($objTypeTable->ExtraFieldNamesArray as $strColName) { %>
			'<%= $strColName %>',
<% } %><%--%>);

		public static $ExtraColumnValuesArray = array(
<% foreach ($objTypeTable->ExtraPropertyArray as $intKey=>$arrColumns) { %>
			<%= $intKey %> => array (
<% foreach ($arrColumns as $strColName=>$strColValue) { %>
						'<%= $strColName %>' => '<%= str_replace("'", "\\'", $strColValue) %>',
<% } %><%--%>),
<% } %><%--%>);


<%}%>
		public static function ToString($int<%= $objTypeTable->ClassName %>Id) {
			switch ($int<%= $objTypeTable->ClassName %>Id) {
<% foreach ($objTypeTable->NameArray as $intKey=>$strValue) { %>
				case <%= $intKey %>: return '<%= $strValue %>';
<% } %>
				default:
					throw new QCallerException(sprintf('Invalid int<%= $objTypeTable->ClassName %>Id: %s', $int<%= $objTypeTable->ClassName %>Id));
			}
		}

		public static function ToToken($int<%= $objTypeTable->ClassName %>Id) {
			switch ($int<%= $objTypeTable->ClassName %>Id) {
<% foreach ($objTypeTable->TokenArray as $intKey=>$strValue) { %>
				case <%= $intKey %>: return '<%= $strValue %>';
<% } %>
				default:
					throw new QCallerException(sprintf('Invalid int<%= $objTypeTable->ClassName %>Id: %s', $int<%= $objTypeTable->ClassName %>Id));
			}
		}

		/**
		 * Returns the Id value corresponding to the Token or the Name value being passed in.
		 *
		 * If none exists, it will return NULL.
		 *
		 * @param string $strTokenOrName the token or the name value to look up
		 * @return integer or null if it does not exist
		 */
		public static function ToId($strTokenOrName) {
			if (($intId = array_search($strTokenOrName, self::$TokenArray)) !== false) return $intId;
			if (($intId = array_search($strTokenOrName, self::$NameArray)) !== false) return $intId;
			return null;
		}

<% foreach ($objTypeTable->ExtraFieldNamesArray as $strColName) { %>
		public static function To<%= $strColName %>($int<%= $objTypeTable->ClassName %>Id) {
			if (array_key_exists($int<%= $objTypeTable->ClassName %>Id, <%= $objTypeTable->ClassName %>::$ExtraColumnValuesArray))
				return <%= $objTypeTable->ClassName %>::$ExtraColumnValuesArray[$int<%= $objTypeTable->ClassName %>Id]['<%= $strColName %>'];
			else
				throw new QCallerException(sprintf('Invalid int<%= $objTypeTable->ClassName %>Id: %s', $int<%= $objTypeTable->ClassName %>Id));
		}

<% } %>
	}
