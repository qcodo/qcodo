		<% $objManyToManyReferenceTable = $objCodeGen->TableArray[strtolower($objManyToManyReference->AssociatedTable)]; %>
		// Related Many-to-Many Objects' Methods for <%= $objManyToManyReference->ObjectDescription %>
		//-------------------------------------------------------------------

		/**
		 * Gets all many-to-many associated <%= $objManyToManyReference->ObjectDescriptionPlural %> as an array of <%= $objManyToManyReference->VariableType %> objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return <%= $objManyToManyReference->VariableType %>[]
		*/ 
		public function Get<%= $objManyToManyReference->ObjectDescription %>Array($objOptionalClauses = null) {
			if (<%= $objCodeGen->ImplodeObjectArray(' || ', '(is_null($this->', '))', 'VariableName', $objTable->PrimaryKeyColumnArray) %>)
				return array();

			try {
				return <%= $objManyToManyReference->VariableType %>::LoadArrayBy<%= $objManyToManyReference->OppositeObjectDescription %>($this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all many-to-many associated <%= $objManyToManyReference->ObjectDescriptionPlural %>
		 * @return int
		*/ 
		public function Count<%= $objManyToManyReference->ObjectDescriptionPlural %>() {
			if (<%= $objCodeGen->ImplodeObjectArray(' || ', '(is_null($this->', '))', 'VariableName', $objTable->PrimaryKeyColumnArray) %>)
				return 0;

			return <%= $objManyToManyReference->VariableType %>::CountBy<%= $objManyToManyReference->OppositeObjectDescription %>($this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>);
		}

		/**
		 * Checks to see if an association exists with a specific <%= $objManyToManyReference->ObjectDescription %>
		 * @param <%= $objManyToManyReference->VariableType %> $<%= $objManyToManyReference->VariableName %>
		 * @return bool
		*/
		public function Is<%= $objManyToManyReference->ObjectDescription %>Associated(<%= $objManyToManyReference->VariableType %> $<%= $objManyToManyReference->VariableName %>) {
			if (<%= $objCodeGen->ImplodeObjectArray(' || ', '(is_null($this->', '))', 'VariableName', $objTable->PrimaryKeyColumnArray) %>)
				throw new QUndefinedPrimaryKeyException('Unable to call Is<%= $objManyToManyReference->ObjectDescription %>Associated on this unsaved <%= $objTable->ClassName %>.');
			if (<%= $objCodeGen->ImplodeObjectArray(' || ', '(is_null($' . $objManyToManyReference->VariableName . '->', '))', 'PropertyName', $objManyToManyReferenceTable->PrimaryKeyColumnArray) %>)
				throw new QUndefinedPrimaryKeyException('Unable to call Is<%= $objManyToManyReference->ObjectDescription %>Associated on this <%= $objTable->ClassName %> with an unsaved <%= $objManyToManyReferenceTable->ClassName %>.');

			$intRowCount = <%= $objTable->ClassName %>::QueryCount(
				QQ::AndCondition(
					QQ::Equal(QQN::<%= $objTable->ClassName %>()-><%= $objTable->PrimaryKeyColumnArray[0]->PropertyName %>, $this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>),
					QQ::Equal(QQN::<%= $objTable->ClassName %>()-><%= $objManyToManyReference->ObjectDescription %>-><%= $objManyToManyReference->OppositePropertyName %>, $<%= $objManyToManyReference->VariableName %>-><%= $objManyToManyReferenceTable->PrimaryKeyColumnArray[0]->PropertyName %>)
				)
			);

			return ($intRowCount > 0);
		}

		/**
		 * Journals the <%= $objManyToManyReference->ObjectDescription %> relationship into the Log database.
		 * Used internally as a helper method.
		 * @param string $strJournalCommand
		 */
		public function Journal<%= $objManyToManyReference->ObjectDescription %>Association($intAssociatedId, $strJournalCommand) {
			$objDatabase = <%= $objTable->ClassName %>::GetDatabase()->JournalingDatabase;

			$objDatabase->NonQuery('
				INSERT INTO <%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Table %><%= $strEscapeIdentifierEnd %> (
					<%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Column %><%= $strEscapeIdentifierEnd %>,
					<%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->OppositeColumn %><%= $strEscapeIdentifierEnd %>,
					__sys_login_id,
					__sys_action,
					__sys_date
				) VALUES (
					' . $objDatabase->SqlVariable($this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>) . ',
					' . $objDatabase->SqlVariable($intAssociatedId) . ',
					' . (($objDatabase->JournaledById) ? $objDatabase->JournaledById : 'NULL') . ',
					' . $objDatabase->SqlVariable($strJournalCommand) . ',
					NOW()
				);
			');
		}

		/**
		 * Gets the historical journal for an object's <%= $objManyToManyReference->ObjectDescription %> relationship from the log database.
		 * @param integer <%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>
		 * @return QDatabaseResult $objResult
		 */
		public static function GetJournal<%= $objManyToManyReference->ObjectDescription %>AssociationForId($<%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>) {
			$objDatabase = <%= $objTable->ClassName %>::GetDatabase()->JournalingDatabase;

			return $objDatabase->Query('SELECT * FROM <%= $objManyToManyReference->Table %> WHERE <%= $objManyToManyReference->Column %> = ' .
				$objDatabase->SqlVariable($<%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>) . ' ORDER BY __sys_date');
		}

		/**
		 * Gets the historical journal for this object's <%= $objManyToManyReference->ObjectDescription %> relationship from the log database.
		 * @return QDatabaseResult $objResult
		 */
		public function GetJournal<%= $objManyToManyReference->ObjectDescription %>Association() {
			return <%= $objTable->ClassName %>::GetJournal<%= $objManyToManyReference->ObjectDescription %>AssociationForId($this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>);
		}

		/**
		 * Associates a <%= $objManyToManyReference->ObjectDescription %>
		 * @param <%= $objManyToManyReference->VariableType %> $<%= $objManyToManyReference->VariableName %>
		 * @return void
		*/ 
		public function Associate<%= $objManyToManyReference->ObjectDescription %>(<%= $objManyToManyReference->VariableType %> $<%= $objManyToManyReference->VariableName %>) {
			if (<%= $objCodeGen->ImplodeObjectArray(' || ', '(is_null($this->', '))', 'VariableName', $objTable->PrimaryKeyColumnArray) %>)
				throw new QUndefinedPrimaryKeyException('Unable to call Associate<%= $objManyToManyReference->ObjectDescription %> on this unsaved <%= $objTable->ClassName %>.');
			if (<%= $objCodeGen->ImplodeObjectArray(' || ', '(is_null($' . $objManyToManyReference->VariableName . '->', '))', 'PropertyName', $objManyToManyReferenceTable->PrimaryKeyColumnArray) %>)
				throw new QUndefinedPrimaryKeyException('Unable to call Associate<%= $objManyToManyReference->ObjectDescription %> on this <%= $objTable->ClassName %> with an unsaved <%= $objManyToManyReferenceTable->ClassName %>.');

			// Get the Database Object for this Class
			$objDatabase = <%= $objTable->ClassName %>::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				INSERT INTO <%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Table %><%= $strEscapeIdentifierEnd %> (
					<%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Column %><%= $strEscapeIdentifierEnd %>,
					<%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->OppositeColumn %><%= $strEscapeIdentifierEnd %>
				) VALUES (
					' . $objDatabase->SqlVariable($this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>) . ',
					' . $objDatabase->SqlVariable($<%= $objManyToManyReference->VariableName %>-><%= $objManyToManyReferenceTable->PrimaryKeyColumnArray[0]->PropertyName %>) . '
				)
			');

			// Journaling (if applicable)
			if ($objDatabase->JournalingDatabase)
				$this->Journal<%= $objManyToManyReference->ObjectDescription %>Association($<%= $objManyToManyReference->VariableName %>-><%= $objManyToManyReferenceTable->PrimaryKeyColumnArray[0]->PropertyName %>, 'INSERT');
		}

		/**
		 * Unassociates a <%= $objManyToManyReference->ObjectDescription %>
		 * @param <%= $objManyToManyReference->VariableType %> $<%= $objManyToManyReference->VariableName %>
		 * @return void
		*/ 
		public function Unassociate<%= $objManyToManyReference->ObjectDescription %>(<%= $objManyToManyReference->VariableType %> $<%= $objManyToManyReference->VariableName %>) {
			if (<%= $objCodeGen->ImplodeObjectArray(' || ', '(is_null($this->', '))', 'VariableName', $objTable->PrimaryKeyColumnArray) %>)
				throw new QUndefinedPrimaryKeyException('Unable to call Unassociate<%= $objManyToManyReference->ObjectDescription %> on this unsaved <%= $objTable->ClassName %>.');
			if (<%= $objCodeGen->ImplodeObjectArray(' || ', '(is_null($' . $objManyToManyReference->VariableName . '->', '))', 'PropertyName', $objManyToManyReferenceTable->PrimaryKeyColumnArray) %>)
				throw new QUndefinedPrimaryKeyException('Unable to call Unassociate<%= $objManyToManyReference->ObjectDescription %> on this <%= $objTable->ClassName %> with an unsaved <%= $objManyToManyReferenceTable->ClassName %>.');

			// Get the Database Object for this Class
			$objDatabase = <%= $objTable->ClassName %>::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					<%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Table %><%= $strEscapeIdentifierEnd %>
				WHERE
					<%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Column %><%= $strEscapeIdentifierEnd %> = ' . $objDatabase->SqlVariable($this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>) . ' AND
					<%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->OppositeColumn %><%= $strEscapeIdentifierEnd %> = ' . $objDatabase->SqlVariable($<%= $objManyToManyReference->VariableName %>-><%= $objManyToManyReferenceTable->PrimaryKeyColumnArray[0]->PropertyName %>) . '
			');

			// Journaling (if applicable)
			if ($objDatabase->JournalingDatabase)
				$this->Journal<%= $objManyToManyReference->ObjectDescription %>Association($<%= $objManyToManyReference->VariableName %>-><%= $objManyToManyReferenceTable->PrimaryKeyColumnArray[0]->PropertyName %>, 'DELETE');
		}

		/**
		 * Unassociates all <%= $objManyToManyReference->ObjectDescriptionPlural %>
		 * @return void
		*/ 
		public function UnassociateAll<%= $objManyToManyReference->ObjectDescriptionPlural %>() {
			if (<%= $objCodeGen->ImplodeObjectArray(' || ', '(is_null($this->', '))', 'VariableName', $objTable->PrimaryKeyColumnArray) %>)
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateAll<%= $objManyToManyReference->ObjectDescription %>Array on this unsaved <%= $objTable->ClassName %>.');

			// Get the Database Object for this Class
			$objDatabase = <%= $objTable->ClassName %>::GetDatabase();

			// Journaling (if applicable)
			if ($objDatabase->JournalingDatabase) {
				$objResult = $objDatabase->Query('SELECT <%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->OppositeColumn %><%= $strEscapeIdentifierEnd %> AS associated_id FROM <%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Table %><%= $strEscapeIdentifierEnd %> WHERE <%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Column %><%= $strEscapeIdentifierEnd %> = ' . $objDatabase->SqlVariable($this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>));
				while ($objRow = $objResult->GetNextRow()) {
					$this->Journal<%= $objManyToManyReference->ObjectDescription %>Association($objRow->GetColumn('associated_id'), 'DELETE');
				}
			}

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					<%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Table %><%= $strEscapeIdentifierEnd %>
				WHERE
					<%= $strEscapeIdentifierBegin %><%= $objManyToManyReference->Column %><%= $strEscapeIdentifierEnd %> = ' . $objDatabase->SqlVariable($this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>) . '
			');
		}