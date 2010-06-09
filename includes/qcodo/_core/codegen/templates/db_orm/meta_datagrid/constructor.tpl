/**
		 * Standard DataGrid constructor which also pre-configures the DataBinder
		 * to its own BindAllRows method (which can obviousy be switched to something else).
		 *
		 * Also pre-configures UseAjax to true.
		 *
		 * @param mixed $objParentObject either a QPanel or QForm which would be this DataGrid's parent
		 * @param string $strControlId optional explicitly-defined ControlId for this DataGrid
		 */
		public function __construct($objParentObject, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);
			$this->SetDataBinder('BindAllRows', $this);
			$this->UseAjax = true;
		}