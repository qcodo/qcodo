////////////////////////////////////////
		// SUPPORT for MULTITON
		////////////////////////////////////////

		/**
		 * @var <%= $objTable->ClassName %>[] $__objInstanceByPrimaryKey
		 */
		private static $__objInstanceByPrimaryKey = [];

		/**
		 * When cloning, this will create a NEW instance as non-restored with no ID information
		 */
		public function __clone() {
			$this->__blnRestored = false;
<% foreach ($objTable->ColumnArray as $objColumn) { %><% if ($objColumn->PrimaryKey) { %>
			$this-><%= $objColumn->VariableName %> = null;
<% } %><% } %>
		}

		/**
		 * Load a <%= $objTable->ClassName %> from Multiton (Application State) via PK Info
<% foreach ($objTable->ColumnArray as $objColumn) { %>
	<% if ($objColumn->PrimaryKey) { %>
		 * @param <%= $objColumn->VariableType %> $<%= $objColumn->VariableName %>
	<% } %>
<% } %>
		 * @return <%= $objTable->ClassName %>|null
		 */
		public static function LoadByMultiton(<%= $objCodeGen->ParameterListFromColumnArray($objTable->PrimaryKeyColumnArray); %>) {
<% foreach ($objTable->ColumnArray as $objColumn) { %><% if ($objColumn->PrimaryKey) { %>
			$mixMultitonKey = $<%= $objColumn->VariableName %>;
<% } %><% } %>

			if (array_key_exists($mixMultitonKey, self::$__objInstanceByPrimaryKey)) return self::$__objInstanceByPrimaryKey[$mixMultitonKey];
			return null;
		}

<% foreach ($objTable->ReverseReferenceArray as $objReverseReference) { %><% if ($objReverseReference->Unique) { %><% $objReverseReferenceTable = $objCodeGen->TableArray[strtolower($objReverseReference->Table)]; %><% $objReverseReferenceColumn = $objReverseReferenceTable->ColumnArray[strtolower($objReverseReference->Column)]; %>
		/**
		 * Clears the reverse unique reference <%= $objReverseReference->ObjectPropertyName %> of an instantiated object (if applicable)
		 * Primarily used by <%= $objReverseReference->VariableType %> whenever <%= $objReverseReference->VariableType %>.<%= $objReverseReference->PropertyName %> is updated
		 * @param mixed $key the primary key / index value of this object
		 */
		public static function ClearReverseReference_<%= $objReverseReference->ObjectPropertyName %>($key) {
			if (array_key_exists($key, self::$__objInstanceByPrimaryKey)) {
				self::$__objInstanceByPrimaryKey[$key]-><%= $objReverseReference->ObjectMemberVariable %> = null;
				self::$__objInstanceByPrimaryKey[$key]->blnDirty<%= $objReverseReference->ObjectPropertyName %> = false;
			}
		}
<% } %><% } %>
