/**
		 * Main utility method to aid with data binding.  It is used by the default BindAllRows() databinder but
		 * could and should be used by any custom databind methods that would be used for instances of this
		 * MetaDataGrid, by simply passing in a custom QQCondition and/or QQClause. 
		 *
		 * If a paginator is set on this DataBinder, it will use it.  If not, then no pagination will be used.
		 * It will also perform any sorting (if applicable).
		 *
		 * @param QQCondition $objConditions override the default condition of QQ::All() to the query, itself
		 * @param QQClause[] $objOptionalClauses additional optional QQClause object or array of QQClause objects for the query		 
		 * @return void
		 */
		public function MetaDataBinder(QQCondition $objCondition = null, $objOptionalClauses = null) {
			// Setup input parameters to default values if none passed in
			if (!$objCondition) $objCondition = QQ::All();
			$objClauses = ($objOptionalClauses) ? $objOptionalClauses : array();

			// We need to first set the TotalItemCount, which will affect the calcuation of LimitClause below
			if ($this->Paginator) $this->TotalItemCount = <%= $objTable->ClassName %>::QueryCount($objCondition, $objClauses);

			// If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
			// the OrderByClause to the $objClauses array
			if ($objClause = $this->OrderByClause) array_push($objClauses, $objClause);

			// Add the LimitClause information, as well
			if ($objClause = $this->LimitClause) array_push($objClauses, $objClause);

			// Set the DataSource to be a Query result from <%= $objTable->ClassName %>, given the clauses above
			$this->DataSource = <%= $objTable->ClassName %>::QueryArray($objCondition, $objClauses);
		}
