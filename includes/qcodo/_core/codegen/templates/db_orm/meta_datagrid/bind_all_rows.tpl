/**
		 * Default / simple DataBinder for this Meta DataGrid.  This can easily be overridden
		 * by calling SetDataBinder() on this DataGrid with another DataBinder of your choice.
		 *
		 * @param QPaginatedControl $objPaginatedControl the QDataGrid object being bound, which will essentially be the same as $this
		 * @return void
		 */
		public function BindAllRows(QPaginatedControl $objPaginatedControl) {
			// Use MetaDataBinder to Bind QQ::All() to this datagrid
			// Expand References (if any) to make fewer database calls
			$objClausesArray = array();
			<% foreach ($objTable->ColumnArray as $objColumn) { %>
				<% if (($objColumn->Reference) && (!$objColumn->Reference->IsType)) { %>
			array_push($objClausesArray, QQ::Expand(QQN::<%= $objTable->ClassName %>()-><%= $objColumn->Reference->PropertyName %>));
				<% } %>
			<% } %>
			$this->MetaDataBinder(QQ::All(), $objClausesArray);
		}