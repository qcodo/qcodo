/**
		 * Default / simple DataBinder for this Meta DataGrid.  This can easily be overridden
		 * by calling SetDataBinder() on this DataGrid with another DataBinder of your choice.
		 *
		 * @param QPaginatedControl $objPaginatedControl the QDataGrid object being bound, which will essentially be the same as $this
		 * @return void
		 */
		public function BindAllRows(QPaginatedControl $objPaginatedControl) {
			// Use MetaDataBinder to Bind QQ::All() to this datagrid
			// Don't pass in any additional / optional clauses
			$this->MetaDataBinder(QQ::All(), null);
		}