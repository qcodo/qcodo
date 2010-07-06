// General Variables
		/**
		 * @var <%= $objTable->ClassName %>
		 * @access protected <%= $objCodeGen->VariableNameFromTable($objTable->Name); %>
		 */
		protected $<%= $objCodeGen->VariableNameFromTable($objTable->Name); %>;

		/**
		 * @var QForm|QControl
		 * @access protected objParentObject
		 */
		protected $objParentObject;

		/**
		 * @var string
		 * @access protected strTitleVerb
		 */
		protected $strTitleVerb;

		/**
		 * @var boolean
		 * @access protected blnEditMode
		 */
		protected $blnEditMode;

		// Controls that allow the editing of <%= $objTable->ClassName %>'s individual data fields
<% foreach ($objTable->ColumnArray as $objColumn) { %>
        /**
         * @var <%= $objCodeGen->FormControlTypeForColumn($objColumn); %> <%= $objCodeGen->FormLabelVariableNameForColumn($objColumn); %>
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