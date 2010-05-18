		/**
		 * Create and setup QListBox <%= $strControlId %>
		 * @param string $strControlId optional ControlId to use
		 * @param QQCondition $objConditions override the default condition of QQ::All() to the query, itself
		 * @param QQClause[] $objOptionalClauses additional optional QQClause object or array of QQClause objects for the query
		 * @return QListBox
		 */
		public function <%= $strControlId %>_Create($strControlId = null, QQCondition $objCondition = null, $objOptionalClauses = null) {
			$this-><%= $strControlId %> = new QListBox($this->objParentObject, $strControlId);
			$this-><%= $strControlId %>->Name = QApplication::Translate('<%= QConvertNotation::WordsFromCamelCase($objColumn->Reference->PropertyName) %>');
<% if ($objColumn->NotNull) { %>
			$this-><%=$strControlId %>->Required = true;
			if (!$this->blnEditMode)
				$this-><%=$strControlId %>->AddItem(QApplication::Translate('- Select One -'), null);
<% } %><% if (!$objColumn->NotNull) { %>
			$this-><%=$strControlId %>->AddItem(QApplication::Translate('- Select One -'), null);
<% } %>

			// Setup and perform the Query
			if (is_null($objCondition)) $objCondition = QQ::All();
			$<%= $objColumn->Reference->VariableName %>Cursor = <%= $objColumn->Reference->VariableType %>::QueryCursor($objCondition, $objOptionalClauses);

			// Iterate through the Cursor
			while ($<%= $objColumn->Reference->VariableName %> = <%= $objColumn->Reference->VariableType %>::InstantiateCursor($<%= $objColumn->Reference->VariableName %>Cursor)) {
				$objListItem = new QListItem($<%= $objColumn->Reference->VariableName %>->__toString(), $<%= $objColumn->Reference->VariableName %>-><%= $objCodeGen->GetTable($objColumn->Reference->Table)->PrimaryKeyColumnArray[0]->PropertyName %>);
				if (($this-><%= $strObjectName %>-><%= $objColumn->Reference->PropertyName %>) && ($this-><%= $strObjectName %>-><%= $objColumn->Reference->PropertyName %>-><%= $objCodeGen->GetTable($objColumn->Reference->Table)->PrimaryKeyColumnArray[0]->PropertyName %> == $<%= $objColumn->Reference->VariableName %>-><%= $objCodeGen->GetTable($objColumn->Reference->Table)->PrimaryKeyColumnArray[0]->PropertyName %>))
					$objListItem->Selected = true;
				$this-><%=$strControlId %>->AddItem($objListItem);
			}

			// Return the QListBox
			return $this-><%= $strControlId %>;
		}

		/**
		 * Create and setup QLabel <%= $strLabelId %>
		 * @param string $strControlId optional ControlId to use
		 * @return QLabel
		 */
		public function <%= $strLabelId %>_Create($strControlId = null) {
			$this-><%= $strLabelId %> = new QLabel($this->objParentObject, $strControlId);
			$this-><%= $strLabelId %>->Name = QApplication::Translate('<%= QConvertNotation::WordsFromCamelCase($objColumn->Reference->PropertyName) %>');
			$this-><%= $strLabelId %>->Text = ($this-><%= $strObjectName %>-><%= $objColumn->Reference->PropertyName %>) ? $this-><%= $strObjectName %>-><%= $objColumn->Reference->PropertyName %>->__toString() : null;
<% if ($objColumn->NotNull) { %>
			$this-><%=$strLabelId %>->Required = true;
<% } %>
			return $this-><%= $strLabelId %>;
		}