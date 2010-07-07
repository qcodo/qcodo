// General Variables
		/**
		 * @var <%= $objTable->ClassName %> <%= $objCodeGen->VariableNameFromTable($objTable->Name); %>
		 * @access protected
		 */
		protected $<%= $objCodeGen->VariableNameFromTable($objTable->Name); %>;

		/**
		 * @var QForm|QControl objParentObject
		 * @access protected
		 */
		protected $objParentObject;

		/**
		 * @var string  strTitleVerb
		 * @access protected
		 */
		protected $strTitleVerb;

		/**
		 * @var boolean blnEditMode
		 * @access protected
		 */
		protected $blnEditMode;

		// Controls that allow the editing of <%= $objTable->ClassName %>'s individual data fields
<% foreach ($objTable->ColumnArray as $objColumn) { %>
        /**
         * @var <%= $objCodeGen->FormControlClassForColumn($objColumn); %> <%= $objCodeGen->FormControlVariableNameForColumn($objColumn); %>;
         * @access protected
         */
		protected $<%= $objCodeGen->FormControlVariableNameForColumn($objColumn); %>;

<% } %>

		// Controls that allow the viewing of <%= $objTable->ClassName %>'s individual data fields
<% foreach ($objTable->ColumnArray as $objColumn) { %>
<% if (!$objColumn->Identity && !$objColumn->Timestamp) { %>
        /**
         * @var QLabel <%= $objCodeGen->FormLabelVariableNameForColumn($objColumn); %>
         * @access protected
         */
		protected $<%= $objCodeGen->FormLabelVariableNameForColumn($objColumn); %>;

<% } %>
<% } %>

		// QListBox Controls (if applicable) to edit Unique ReverseReferences and ManyToMany References
<% foreach ($objTable->ReverseReferenceArray as $objReverseReference) { %>
	<% if ($objReverseReference->Unique) { %>
        /**
         * @var QListBox <%= $objCodeGen->FormControlVariableNameForUniqueReverseReference($objReverseReference); %>
         * @access protected
         */
		protected $<%= $objCodeGen->FormControlVariableNameForUniqueReverseReference($objReverseReference); %>;

	<% } %>
<% } %>
<% foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) { %>
		protected $<%= $objCodeGen->FormControlVariableNameForManyToManyReference($objManyToManyReference); %>;

<% } %>

		// QLabel Controls (if applicable) to view Unique ReverseReferences and ManyToMany References
<% foreach ($objTable->ReverseReferenceArray as $objReverseReference) { %>
	<% if ($objReverseReference->Unique) { %>
        /**
         * @var QLabel <%= $objCodeGen->FormLabelVariableNameForUniqueReverseReference($objReverseReference); %>
         * @access protected
         */
		protected $<%= $objCodeGen->FormLabelVariableNameForUniqueReverseReference($objReverseReference); %>;

	<% } %>
<% } %>
<% foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) { %>
		protected $<%= $objCodeGen->FormLabelVariableNameForManyToManyReference($objManyToManyReference); %>;

<% } %>