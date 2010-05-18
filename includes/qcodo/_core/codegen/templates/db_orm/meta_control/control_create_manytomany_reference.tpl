		/**
		 * Create and setup QListBox <%= $strControlId %>
		 * @param string $strControlId optional ControlId to use
		 * @param QQCondition $objConditions override the default condition of QQ::All() to the query, itself
		 * @param QQClause[] $objOptionalClauses additional optional QQClause object or array of QQClause objects for the query
		 * @return QListBox
		 */
		public function <%= $strControlId %>_Create($strControlId = null, QQCondition $objCondition = null, $objOptionalClauses = null) {
			$this-><%= $strControlId %> = new QListBox($this->objParentObject, $strControlId);
			$this-><%= $strControlId %>->Name = QApplication::Translate('<%= QConvertNotation::WordsFromCamelCase($objManyToManyReference->ObjectDescriptionPlural) %>');
			$this-><%=$strControlId %>->SelectionMode = QSelectionMode::Multiple;

			// We need to know which items to "Pre-Select"
			$objAssociatedArray = $this-><%= $strObjectName %>->Get<%= $objManyToManyReference->ObjectDescription; %>Array();

			// Setup and perform the Query
			if (is_null($objCondition)) $objCondition = QQ::All();
			$<%= $objManyToManyReference->VariableName %>Cursor = <%= $objManyToManyReference->VariableType %>::QueryCursor($objCondition, $objOptionalClauses);

			// Iterate through the Cursor
			while ($<%= $objManyToManyReference->VariableName %> = <%= $objManyToManyReference->VariableType %>::InstantiateCursor($<%= $objManyToManyReference->VariableName %>Cursor)) {
				$objListItem = new QListItem($<%= $objManyToManyReference->VariableName %>->__toString(), $<%= $objManyToManyReference->VariableName %>-><%= $objCodeGen->GetTable($objManyToManyReference->AssociatedTable)->PrimaryKeyColumnArray[0]->PropertyName %>);
				foreach ($objAssociatedArray as $objAssociated) {
					if ($objAssociated-><%= $objCodeGen->GetTable($objManyToManyReference->AssociatedTable)->PrimaryKeyColumnArray[0]->PropertyName %> == $<%= $objManyToManyReference->VariableName %>-><%= $objCodeGen->GetTable($objManyToManyReference->AssociatedTable)->PrimaryKeyColumnArray[0]->PropertyName %>)
						$objListItem->Selected = true;
				}
				$this-><%=$strControlId %>->AddItem($objListItem);
			}

			// Return the QListControl
			return $this-><%= $strControlId %>;
		}

		/**
		 * Create and setup QLabel <%= $strLabelId %>
		 * @param string $strControlId optional ControlId to use
		 * @param string $strGlue glue to display in between each associated object
		 * @return QLabel
		 */
		public function <%= $strLabelId %>_Create($strControlId = null, $strGlue = ', ') {
			$this-><%= $strLabelId %> = new QLabel($this->objParentObject, $strControlId);
			$this-><%= $strControlId %>->Name = QApplication::Translate('<%= QConvertNotation::WordsFromCamelCase($objManyToManyReference->ObjectDescriptionPlural) %>');
			
			$objAssociatedArray = $this-><%= $strObjectName %>->Get<%= $objManyToManyReference->ObjectDescription; %>Array();
			$strItems = array();
			foreach ($objAssociatedArray as $objAssociated)
				$strItems[] = $objAssociated->__toString();
			$this-><%= $strLabelId %>->Text = implode($strGlue, $strItems);
			return $this-><%= $strLabelId %>;
		}