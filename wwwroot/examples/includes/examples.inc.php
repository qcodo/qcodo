<?php
	class Examples {
		public static $Categories = array();
		public static $AdditionalCode = array();

		public static function Init() {
			$intIndex = -1;

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Basic CodeGen';
			Examples::$Categories[$intIndex]['description'] = 'An introduction to the Qcodo Code Generator';
			array_push(Examples::$Categories[$intIndex], '/code_generator/about.php (About Sections 1 - 3)');
			array_push(Examples::$Categories[$intIndex], '/code_generator/intro.php About the Database');
			array_push(Examples::$Categories[$intIndex], '/code_generator/objects.php * Object Relational Model');
			array_push(Examples::$Categories[$intIndex], '/code_generator/indexes.php * Loading Objects');
			array_push(Examples::$Categories[$intIndex], '/code_generator/save_delete.php * Saving and Deleting');
			array_push(Examples::$Categories[$intIndex], '/code_generator/relationships.php * Relationships (Foreign Keys)');
			array_push(Examples::$Categories[$intIndex], '/code_generator/reverse_relationships.php * Reverse Relationships (One-to-One and One-to-Many)');
			array_push(Examples::$Categories[$intIndex], '/code_generator/relationships_many.php * Many-to-Many Relationships');
			array_push(Examples::$Categories[$intIndex], '/code_generator/script_path.php Defining Relationships without Foreign Keys');
			array_push(Examples::$Categories[$intIndex], '/code_generator/primary_keys.php Explanation of Qcodo\'s Primary Key Requirement');
			Examples::$AdditionalCode['/code_generator/intro.php'] = array('mysql_innodb.sql','sql_server.sql');
			Examples::$AdditionalCode['/code_generator/script_path.php'] = array('mysql_myisam.sql', 'relationships.txt');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'More About the Object Relational Model';
			Examples::$Categories[$intIndex]['description'] = 'Looking more in depth at the functionality of the generated ORM';
			array_push(Examples::$Categories[$intIndex], '/more_codegen/sort_limit.php * Sorting and Limiting Array Results');
			array_push(Examples::$Categories[$intIndex], '/more_codegen/late_bind.php * Late Binding of Related Objects');
			array_push(Examples::$Categories[$intIndex], '/more_codegen/early_bind.php * Early Binding of Related Objects');
			array_push(Examples::$Categories[$intIndex], '/more_codegen/virtual_attributes.php * Virtual Attributes');
			array_push(Examples::$Categories[$intIndex], '/more_codegen/type_tables.php * Type Tables');
			array_push(Examples::$Categories[$intIndex], '/more_codegen/custom.php * Customized Business Logic');
			array_push(Examples::$Categories[$intIndex], '/more_codegen/custom_load.php * Customized Load Methods');
			array_push(Examples::$Categories[$intIndex], '/more_codegen/optimistic_locking.php * Optimistic Locking');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Querying in Qcodo';
			Examples::$Categories[$intIndex]['description'] = 'Ad Hoc Queries, Custom Load Methods, and <b>Qcodo Query</b>';
			array_push(Examples::$Categories[$intIndex], '/qcodo_query/intro.php * Performing Custom SQL Queries');
			array_push(Examples::$Categories[$intIndex], '/qcodo_query/qq.php * Qcodo Query: Object Oriented Database Querying');
			array_push(Examples::$Categories[$intIndex], '/qcodo_query/qqnode.php * Qcodo Query: The QQ Node Classes');
			array_push(Examples::$Categories[$intIndex], '/qcodo_query/qqcondition.php * Qcodo Query: The QQ Condition Classes');
			array_push(Examples::$Categories[$intIndex], '/qcodo_query/qqclause.php * Qcodo Query: The QQ Clause Classes');
			array_push(Examples::$Categories[$intIndex], '/qcodo_query/association.php * Qcodo Query: Handling Association Tables');
			array_push(Examples::$Categories[$intIndex], '/qcodo_query/reverse.php * Qcodo Query: Handling Reverse Relationships');
			array_push(Examples::$Categories[$intIndex], '/qcodo_query/migrating.php * From Beta 2: Migrating from "Manual Queries"');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Basic QForms';
			Examples::$Categories[$intIndex]['description'] = 'An introduction to QForms and QControls';
			array_push(Examples::$Categories[$intIndex], '/basic_qform/about.php (About Sections 4 - 10)');
			array_push(Examples::$Categories[$intIndex], '/basic_qform/intro.php Hello World Example');
			array_push(Examples::$Categories[$intIndex], '/basic_qform/state.php QForms: Stateful, Event-Driven Objects');
			array_push(Examples::$Categories[$intIndex], '/basic_qform/process_flow.php Understanding Process Flow');
			array_push(Examples::$Categories[$intIndex], '/basic_qform/calculator.php Calculator Example');
			array_push(Examples::$Categories[$intIndex], '/basic_qform/calculator_2.php Calculator Example with Validation');
			array_push(Examples::$Categories[$intIndex], '/basic_qform/calculator_3.php Calculator Example with &quot;Design&quot;');
			array_push(Examples::$Categories[$intIndex], '/basic_qform/listbox.php * Introduction to QListControl');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Basic AJAX in QForms';
			Examples::$Categories[$intIndex]['description'] = 'A look at how to AJAX-enable your QForms';
			array_push(Examples::$Categories[$intIndex], '/basic_ajax/intro.php Hello World Example using AJAX');
			array_push(Examples::$Categories[$intIndex], '/basic_ajax/calculator_2.php Calculator Example using AJAX');
			array_push(Examples::$Categories[$intIndex], '/basic_ajax/wait_icon.php Adding a Wait Icon');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'More About Events and Actions';
			Examples::$Categories[$intIndex]['description'] = 'Looking more in depth at the capabilities of the QEvent and QAction libraries';
			array_push(Examples::$Categories[$intIndex], '/events_actions/editable_listbox.php Editable ListBox');
			array_push(Examples::$Categories[$intIndex], '/events_actions/editable_listbox_2.php Conditional Events');
			array_push(Examples::$Categories[$intIndex], '/events_actions/delayed.php Trigger-Delayed Events');
			array_push(Examples::$Categories[$intIndex], '/events_actions/javascript_alerts.php Javascript Actions, Alerts and Confirmations');
			array_push(Examples::$Categories[$intIndex], '/events_actions/other_actions.php Other Client-Side QActions');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Paginated Controls';
			Examples::$Categories[$intIndex]['description'] = 'The QDataGrid and QDataRepeater controls';
			array_push(Examples::$Categories[$intIndex], '/datagrid/intro.php * Basic QDataGrid');
			array_push(Examples::$Categories[$intIndex], '/datagrid/variables.php * The QDataGrid Variables');
			array_push(Examples::$Categories[$intIndex], '/datagrid/sorting.php * QDataGrid Sorting');
			array_push(Examples::$Categories[$intIndex], '/datagrid/pagination.php * QDataGrid Pagination');
			array_push(Examples::$Categories[$intIndex], '/datagrid/ajax.php * Enabling AJAX on the QDataGrid');
			array_push(Examples::$Categories[$intIndex], '/datarepeater/ajax.php * Simple QDataRepeater using AJAX-triggered Pagination');
			array_push(Examples::$Categories[$intIndex], '/datagrid/extend.php * Creating Your Own Custom QDataGrid Subclass');
			Examples::$AdditionalCode['/datarepeater/ajax.php'] = array('dtr_persons.tpl.php');
			Examples::$AdditionalCode['/datagrid/extend.php'] = array('QDataGrid.class.php');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Advanced Controls Manipulation';
			Examples::$Categories[$intIndex]['description'] = 'Dynamically creating controls, Implementing custom controls';
			array_push(Examples::$Categories[$intIndex], '/dynamic/select.php * Creating Checkboxes in a Datagrid');
			array_push(Examples::$Categories[$intIndex], '/dynamic/inline_editing.php * Datagrid with Inline Editing');
			array_push(Examples::$Categories[$intIndex], '/dynamic/qpanel.php Introduction to QBlockControls');
			array_push(Examples::$Categories[$intIndex], '/dynamic/qpanel_2.php Dynamically Changing a Control\'s Parent');
			array_push(Examples::$Categories[$intIndex], '/other_controls/sample.php Creating Your Own Control');
			array_push(Examples::$Categories[$intIndex], '/composite/intro.php Creating a Composite Control');
			array_push(Examples::$Categories[$intIndex], '/multiple_qform/intro.php "Multiple QForms" Functionality via Custom QPanels');
			array_push(Examples::$Categories[$intIndex], '/dynamic/control_proxy.php Using QControlProxies to have Non-QControls Trigger Events');
			Examples::$AdditionalCode['/dynamic/qpanel.php'] = array('pnl_panel.tpl.php');
			Examples::$AdditionalCode['/other_controls/sample.php'] = array('___QSampleControl.class.php');
			Examples::$AdditionalCode['/composite/intro.php'] = array('SampleComposite.class.php');
			Examples::$AdditionalCode['/multiple_qform/intro.php'] = array(
				'ProjectViewPanel.class.php', 'ProjectViewPanel.tpl.php',
				'ProjectEditPanel.class.php', 'ProjectEditPanel.tpl.php',
				'PersonEditPanel.class.php', 'PersonEditPanel.tpl.php');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Advanced AJAX';
			Examples::$Categories[$intIndex]['description'] = 'Advanced AJAX functionality like drag and drop, selection and cinematic effects';
			array_push(Examples::$Categories[$intIndex], '/advanced_ajax/renamer.php Renameable Labels');
			array_push(Examples::$Categories[$intIndex], '/advanced_ajax/renamer_2.php Porting Code to the Client Side');
			array_push(Examples::$Categories[$intIndex], '/advanced_ajax/move.php Moveable Controls (a.k.a. Drag and Drop)');
			array_push(Examples::$Categories[$intIndex], '/advanced_ajax/scrolling.php Automatic Scrolling');
			array_push(Examples::$Categories[$intIndex], '/advanced_ajax/move_target.php Move Handle: Specifying Which Controls to Move');
			array_push(Examples::$Categories[$intIndex], '/advanced_ajax/drop_zone.php Move Handle: Defining Drop Zones');
			array_push(Examples::$Categories[$intIndex], '/advanced_ajax/resize.php Resizing Block Controls');
			array_push(Examples::$Categories[$intIndex], '/advanced_ajax/dialog_box.php Modal "Dialog Boxes"');
			Examples::$AdditionalCode['/advanced_ajax/dialog_box.php'] = array('CalculatorWidget.class.php','CalculatorWidget.tpl.php');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Other Advanced Controls';
			Examples::$Categories[$intIndex]['description'] = 'A collection of examples for some of the more advanced/complex QControls';
			array_push(Examples::$Categories[$intIndex], '/image_label/intro.php Introduction to QImageLabel');
			array_push(Examples::$Categories[$intIndex], '/treenav/treenav.php Introduction to QTreeNav');
			array_push(Examples::$Categories[$intIndex], '/other_controls/image.php Introduction to QImageControl');
			array_push(Examples::$Categories[$intIndex], '/other_controls/datetime.php Date and DateTime-based QControls');
			array_push(Examples::$Categories[$intIndex], '/other_controls/file_asset.php Combining Controls: A Better Way to Upload Files');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'MetaControls, Meta DataGrids, and the Drafts';
			Examples::$Categories[$intIndex]['description'] = 'Combining the Code Generator with the QForm Library';
			array_push(Examples::$Categories[$intIndex], '/other/formgen.php QForm and CodeGen, a Winning Combination to RAD');
			array_push(Examples::$Categories[$intIndex], '/other/metacontrols.php Introduction to MetaControls');
			array_push(Examples::$Categories[$intIndex], '/other/meta_datagrids.php Introduction to Meta DataGrids');
			array_push(Examples::$Categories[$intIndex], '/other/form_drafts.php Introduction to the Drafts');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Beyond HTML';
			Examples::$Categories[$intIndex]['description'] = 'Qcodo\'s other libraries, including support for Email and RSS';
			array_push(Examples::$Categories[$intIndex], '/communication/email.php Introduction to QEmailServer');
			array_push(Examples::$Categories[$intIndex], '/communication/rss.php * Introduction to QRssFeed');
			array_push(Examples::$Categories[$intIndex], '/communication/crypto.php Introduction to QCryptography');
			array_push(Examples::$Categories[$intIndex], '/communication/i18n.php Introduction to QI18n (Internationalization)');
			array_push(Examples::$Categories[$intIndex], '/communication/soap.php * Introduction to QSoapService');
			Examples::$AdditionalCode['/communication/rss.php'] = array('rss_feed.php');
			Examples::$AdditionalCode['/communication/i18n.php'] = array('en.po', 'es.po');
			Examples::$AdditionalCode['/communication/soap.php'] = array('example_service.php');

			$intIndex++;
			Examples::$Categories[$intIndex] = array();
			Examples::$Categories[$intIndex]['name'] = 'Other Tidbits';
			Examples::$Categories[$intIndex]['description'] = 'Other random examples, samples and tutorials';
			array_push(Examples::$Categories[$intIndex], '/other/optimistic_locking.php * Optimistic Locking and QForms');
			array_push(Examples::$Categories[$intIndex], '/other/attribute_overriding.php Attribute Overriding');
			array_push(Examples::$Categories[$intIndex], '/other/alternate_template.php Specifying a Template Filepath');
			array_push(Examples::$Categories[$intIndex], '/other/single.php Single File QForms');
			array_push(Examples::$Categories[$intIndex], '/other/form_state.php Working with FormState Handlers');
			array_push(Examples::$Categories[$intIndex], '/other/print.php PHP Print Command Shortcuts');
			Examples::$AdditionalCode['/other/alternate_template.php'] = array('some_template_file.tpl.php');
		}

		public static function GetCategoryId() {
			for ($intCategoryIndex = 0; $intCategoryIndex <= count(Examples::$Categories); $intCategoryIndex++) {
				$objExampleCategory = Examples::$Categories[$intCategoryIndex];
				
				for ($intExampleIndex = 0; $intExampleIndex <= count($objExampleCategory); $intExampleIndex++) {
					if (array_key_exists($intExampleIndex, $objExampleCategory)) {
						$strExample = $objExampleCategory[$intExampleIndex];
						$intPosition = strpos($strExample, ' ');
						$strScriptPath = substr($strExample, 0, $intPosition);
						$strName = substr($strExample, $intPosition + 1);

						if (strtolower(substr(QApplicationBase::$ScriptName, strlen(QApplicationBase::$ScriptName) - strlen($strScriptPath))) == strtolower($strScriptPath))
							return $intCategoryIndex;
					}
				}
			}

			return null;
		}

		public static function GetExampleId() {
			for ($intCategoryIndex = 0; $intCategoryIndex < count(Examples::$Categories); $intCategoryIndex++) {
				$objExampleCategory = Examples::$Categories[$intCategoryIndex];
				
				for ($intExampleIndex = 0; $intExampleIndex < count($objExampleCategory); $intExampleIndex++) {
					if (array_key_exists($intExampleIndex, $objExampleCategory)) {
						$strExample = $objExampleCategory[$intExampleIndex];
						$intPosition = strpos($strExample, ' ');
						$strScriptPath = substr($strExample, 0, $intPosition);
						$strName = substr($strExample, $intPosition + 1);

						if (strtolower(substr(QApplicationBase::$ScriptName, strlen(QApplicationBase::$ScriptName) - strlen($strScriptPath))) == strtolower($strScriptPath))
							return $intExampleIndex;
					}
				}
			}

			return null;
		}
		
		public static function GetExampleName($intCategoryId, $intExampleId) {
			$strExample = Examples::$Categories[$intCategoryId][$intExampleId];
			$intPosition = strpos($strExample, ' ');
			$strScriptPath = substr($strExample, 0, $intPosition);
			$strName = substr($strExample, $intPosition + 1);
			return $strName;
		}
		
		public static function GetExampleScriptPath($intCategoryId, $intExampleId) {
			$strExample = Examples::$Categories[$intCategoryId][$intExampleId];
			$intPosition = strpos($strExample, ' ');
			$strScriptPath = substr($strExample, 0, $intPosition);
			$strName = substr($strExample, $intPosition + 1);
			return $strScriptPath;
		}

		public static function PageName($strReference = null) {
			if (is_null($strReference))
				$strReference = QApplication::$ScriptName;

			foreach (Examples::$Categories as $objExampleCategory)
				foreach ($objExampleCategory as $strKey => $strExample)
					if (is_numeric($strKey)) {
						// Pull out the URL fragment from the example tree
						$intPosition = strpos($strExample, ' ');
						$strScriptName = substr($strExample, 0, $intPosition);

						if (strpos($strReference, $strScriptName) !== false)
							return(substr($strExample, $intPosition + 1));
					}

			return 'Main Page';
		}
		
		public static function PageLinkName($strReference = null) {
			if (is_null($strReference))
				$strReference = QApplication::$ScriptName;

			foreach (Examples::$Categories as $objExampleCategory)
				foreach ($objExampleCategory as $strKey => $strExample)
					if (is_numeric($strKey)) {
						// Pull out the URL fragment from the example tree
						$intPosition = strpos($strExample, ' ');
						$strScriptName = substr($strExample, 0, $intPosition);

						if (strpos($strReference, $strScriptName) !== false)
							return($strScriptName);
					}
		}
		
		public static function CodeLinks($strReference, $strCurrentScript) {
			$blnIsScript = false;

			if ($strCurrentScript == 'header.inc.php') {
				$strToReturn = '<span class="headingLeftGray">header.inc.php</span>';
				$blnIsScript = true;
			} else
				$strToReturn = sprintf('<a href="%s/../header.inc.php" class="headingLink">header.inc.php</a>', QApplication::$RequestUri);

			$strToReturn .= ' &nbsp; | &nbsp; ';

			if ($strCurrentScript == 'footer.inc.php') {
				$strToReturn .= '<span class="headingLeftGray">footer.inc.php</span>';
				$blnIsScript = true;
			} else
				$strToReturn .= sprintf('<a href="%s/../footer.inc.php" class="headingLink">footer.inc.php</a>', QApplication::$RequestUri);

			$strToReturn .= ' &nbsp; | &nbsp; ';

			if ($strCurrentScript == 'examples.css') {
				$strToReturn .= '<span class="headingLeftGray">examples.css</span>';
				$blnIsScript = true;
			} else
				$strToReturn .= sprintf('<a href="%s/../examples.css" class="headingLink">examples.css</a>', QApplication::$RequestUri);

			$strToReturn .= ' &nbsp; | &nbsp; ';

			$strScriptname = substr($strReference, strrpos($strReference, '/') + 1);
			if ($strCurrentScript == $strScriptname) {
				$strToReturn .= sprintf('<span class="headingLeftGray">%s</span>', $strScriptname);
				$blnIsScript = true;
			} else
				$strToReturn .= sprintf('<a href="%s/../%s" class="headingLink">%s</a>', QApplication::$RequestUri, $strScriptname, $strScriptname);


			// Current Number of Code Links
			$intCount = 4;

			if (file_exists(substr(str_replace('.php', '.tpl.php', $strReference), 1))) {
				$strToReturn .= ' &nbsp; | &nbsp; ';

				$strScriptname = substr(str_replace('.php', '.tpl.php', $strReference), strrpos(str_replace('.php', '.tpl.php', $strReference), '/') + 1);
				if ($strCurrentScript == $strScriptname) {
					$strToReturn .= sprintf('<span class="headingLeftGray">%s</span>', $strScriptname);
					$blnIsScript = true;
				} else
					$strToReturn .= sprintf('<a href="%s/../%s" class="headingLink">%s</a>', QApplication::$RequestUri, $strScriptname, $strScriptname);

				$intCount++;
			}

			if(array_key_exists($strReference, Examples::$AdditionalCode))
				foreach (Examples::$AdditionalCode[$strReference] as $strCode) {
					if (($intCount % 7) == 0)
						$strToReturn .= '<br/>';
					else
						$strToReturn .= ' &nbsp; | &nbsp; ';
		
					$strScriptname = $strCode;
					if ($strCurrentScript == $strScriptname) {
						$strToReturn .= sprintf('<span class="headingLeftGray">%s</span>', str_replace('___', '', $strScriptname));
						$blnIsScript = true;
					} else
						$strToReturn .= sprintf('<a href="%s/../%s" class="headingLink">%s</a>', QApplication::$RequestUri, $strScriptname, str_replace('___', '', $strScriptname));

					$intCount++;
				}
				
			if ($blnIsScript)
				return $strToReturn;
			else
				QApplication::CloseWindow();
		}

		public static function PageLinks() {
			$strPrevious = null;
			$strNext = null;
			$blnFound = false;

			foreach (Examples::$Categories as $objExampleCategory) {
				if (!$blnFound) {
					$strPrevious = null;
					$strNext = null;

					foreach ($objExampleCategory as $strKey => $strExample) {
						if (is_numeric($strKey)) {
							// Pull out the URL fragment from the example tree
							$intPosition = strpos($strExample, ' ');
							$strScriptName = substr($strExample, 0, $intPosition);
							$strDescription = substr($strExample, $intPosition + 1);

							if (!$blnFound) {
								if (strpos(QApplication::$ScriptName, $strScriptName) !== false) {
									$blnFound = true;
								} else {
									$strPrevious = sprintf('<b><a href="%s%s" class="headingLink">&lt;&lt; %s</a></b>',
										__VIRTUAL_DIRECTORY__ . __EXAMPLES__, $strScriptName, $strDescription);
								}
							} else if (!$strNext) {
								$strNext = sprintf('<b><a href="%s%s" class="headingLink">%s &gt;&gt;</a></b>',
									__VIRTUAL_DIRECTORY__ . __EXAMPLES__, $strScriptName, $strDescription);
							}
						}
					}
				}
			}
			
			$strToReturn = '';
			
			if ($strPrevious)
				$strToReturn = $strPrevious;
			else
				$strToReturn = '<span class="headingLeftGray">&lt;&lt; Previous</span>';

			$intCategoryId = Examples::GetCategoryId();
			if ($intCategoryId < 3)
				$intPartId = 1;
			else if ($intCategoryId < 10)
				$intPartId = 2;
			else
				$intPartId = 3;

			$strToReturn .= ' &nbsp; | &nbsp; ';
			$strToReturn .= sprintf('<b><a href="%s/index.php/%s" class="headingLink">Back to Main</a></b>',
				__VIRTUAL_DIRECTORY__ . __EXAMPLES__, $intPartId);
			$strToReturn .= ' &nbsp; | &nbsp; ';

			if ($strNext)
				$strToReturn .= $strNext;
			else
				$strToReturn .= '<span class="headingLeftGray">Next &gt;&gt;</span>';

			return $strToReturn;				
		}
	}

	Examples::Init();
?>