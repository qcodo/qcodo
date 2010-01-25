<?php
	// Note: This file is usually in /includes/qform/QDataGrid.inc.  However, it has been
	// pulled out and put here for purposes of this Examples Site demo.

	class QDataGrid extends QDataGridBase  {
		// Specify a CssClass
		protected $strCssClass = 'datagrid';

		// Let's Show a Footer
		protected $blnShowFooter = true;
		
		// Let's define the footer to be to display our alternate paginator
		// We'll use the already built-in GetPaginatorRowHtml, sending in our ALTERNATE paginator, to help with the rendering
		protected function GetFooterRowHtml() {
			if ($this->objPaginatorAlternate)
				return sprintf('<tr><td colspan="%s">%s</td></tr>', count($this->objColumnArray), $this->GetPaginatorRowHtml($this->objPaginatorAlternate));
		}
	}
?>