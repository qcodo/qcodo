/**
		 * Journals the current object into the Log database.
		 * Used internally as a helper method.
		 * @param string $strJournalCommand
		 */
		public function Journal($strJournalCommand) {
			$objDatabase = <%= $objTable->ClassName %>::GetDatabase()->JournalingDatabase;

			$objDatabase->NonQuery('
				INSERT INTO <%= $strEscapeIdentifierBegin %><%= $objTable->Name %><%= $strEscapeIdentifierEnd %> (
<% foreach ($objTable->ColumnArray as $objColumn) { %>
<% if (!$objColumn->Timestamp) { %>
					<%= $strEscapeIdentifierBegin %><%= $objColumn->Name %><%= $strEscapeIdentifierEnd %>,
<% } %>
<% } %>
					__sys_login_id,
					__sys_action,
					__sys_date
				) VALUES (
<% foreach ($objTable->ColumnArray as $objColumn) { %>
<% if (!$objColumn->Timestamp) { %>
					' . $objDatabase->SqlVariable($this-><%= $objColumn->VariableName %>) . ',
<% } %>
<% } %>
					' . (($objDatabase->JournaledById) ? $objDatabase->JournaledById : 'NULL') . ',
					' . $objDatabase->SqlVariable($strJournalCommand) . ',
					NOW()
				);
			');
		}

		/**
		 * Gets the historical journal for an object from the log database.
		 * Objects will have VirtualAttributes available to lookup login, date, and action information from the journal object.
		 * @param integer <%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>
		 * @return <%= $objTable->ClassName %>[]
		 */
		public static function GetJournalForId($<%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>) {
			$objDatabase = <%= $objTable->ClassName %>::GetDatabase()->JournalingDatabase;

			$objResult = $objDatabase->Query('SELECT * FROM <%= $objTable->Name %> WHERE <%= $objTable->PrimaryKeyColumnArray[0]->Name %> = ' .
				$objDatabase->SqlVariable($<%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>) . ' ORDER BY __sys_date');

			return <%= $objTable->ClassName %>::InstantiateDbResult($objResult);
		}

		/**
		 * Gets the historical journal for this object from the log database.
		 * Objects will have VirtualAttributes available to lookup login, date, and action information from the journal object.
		 * @return <%= $objTable->ClassName %>[]
		 */
		public function GetJournal() {
			return <%= $objTable->ClassName %>::GetJournalForId($this-><%= $objTable->PrimaryKeyColumnArray[0]->VariableName %>);
		}
