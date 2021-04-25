<?php
	/**
	 * Qcodo Error Dump
	 */

    if (\Qcodo\Handlers\WebService::$HttpRequest) {
        $_SERVER['PHP_SELF'] = \Qcodo\Handlers\WebService::$HttpRequest->path;
	}
?>
<html>
	<head>
		<title>PHP <?php print(QErrorHandler::$Type); ?> - <?php print(QErrorHandler::$Message); ?></title>
		<style>
			body { font-family: 'Arial', 'Helvetica', 'sans-serif'; font-size: 11px; }
			a:link, a:visited { text-decoration: none; }
			a:hover { text-decoration: underline; }
			pre { font-family: 'Lucida Console', 'Courier New', 'Courier', 'monospaced'; font-size: 11px; line-height: 13px; }
			.page { padding: 10px; }
			.headingLeft { background-color: #440066; color: #ffffff; padding: 10px 0px 10px 10px; font-family: 'Verdana', 'Arial', 'Helvetica', 'sans-serif'; font-size: 18px; font-weight: bold; width: 70%; vertical-align: middle; }
			.headingLeftSmall { font-size: 10px; }
			.headingRight { background-color: #440066; color: #ffffff; padding: 0px 10px 10px 10px; font-family: 'Verdana', 'Arial', 'Helvetica', 'sans-serif'; font-size: 10px; width: 30%; vertical-align: middle; text-align: right; }
			.title { font-family: 'Verdana', 'Arial', 'Helvetica', 'sans-serif'; font-size: 19px; font-style: italic; color: #330055; }
			.code { background-color: #f4eeff; padding: 1px 10px 1px 10px; }
		</style>
		<script type="text/javascript">
			function RenderPage(strHtml) {
				var win = window.open("", "Rendered Error Information");
				win.document.body.innerHTML = strHtml;
            }
			function ToggleHidden(strDiv) { var obj = document.getElementById(strDiv); var stlSection = obj.style; var isCollapsed = obj.style.display.length; if (isCollapsed) stlSection.display = ''; else stlSection.display = 'none'; }
		</script>
	</head>
	<body bgcolor="white" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

	<table border="0" cellspacing="0" width="100%">
		<tr>
			<td nowrap="nowrap" class="headingLeft"><span class="headingLeftSmall"><?php print(QErrorHandler::$Type); ?> in PHP Script<br /></span><?php print($_SERVER["PHP_SELF"]); ?></td>
			<td nowrap="nowrap" class="headingRight">
				<b>PHP Version:</b> <?php print(PHP_VERSION); ?>;&nbsp;&nbsp;<b>Zend Engine Version:</b> <?php print(zend_version()); ?>;&nbsp;&nbsp;<b>Qcodo Version:</b> <?php print(QCODO_VERSION); ?><br />
				<?php if (array_key_exists('OS', $_SERVER)) printf('<b>Operating System:</b> %s;&nbsp;&nbsp;', $_SERVER['OS']); ?><b>Application:</b> <?php print($_SERVER['SERVER_SOFTWARE']); ?>;&nbsp;&nbsp;<b>Server Name:</b> <?php print($_SERVER['SERVER_NAME']); ?><br />
				<b>HTTP User Agent:</b> <?php if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) print($_SERVER['HTTP_USER_AGENT']); ?></td>
		</tr>
	</table>

	<div class="page">
		<span class="title"><?php print(QErrorHandler::$MessageBody); ?></span><br />

			<b><?php print(QErrorHandler::$Type); ?> Type:</b>&nbsp;&nbsp;
			<?php print(QErrorHandler::$ObjectType); ?>
			<br /><br />

<?php
			if (isset(QErrorHandler::$RenderedPage)) {
?>
				<script type="text/javascript">RenderedPage = "<?php print(QErrorHandler::PrepDataForScript(QErrorHandler::$RenderedPage)); ?>";</script>
				<b>Rendered Page:</b>&nbsp;&nbsp;
				<a href="javascript:RenderPage(RenderedPage)">Click here</a> to view contents able to be rendered
				<br /><br />
<?php
			}
?>
			<b>Source File:</b>&nbsp;&nbsp;
			<?php print(QErrorHandler::$Filename); ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<b>Line:</b>&nbsp;&nbsp;
			<?php print(QErrorHandler::$LineNumber); ?>
			<br /><br />

			<div class="code">
<?php
						print('<pre>');
						for ($__exc_IntLine = max(1, QErrorHandler::$LineNumber - 5); $__exc_IntLine <= min(count(QErrorHandler::$FileLinesArray), QErrorHandler::$LineNumber + 5); $__exc_IntLine++) {
							if (QErrorHandler::$LineNumber == $__exc_IntLine)
								printf('<span style="color: #f00;">Line %s:    %s</span>', $__exc_IntLine, htmlentities(QErrorHandler::$FileLinesArray[$__exc_IntLine - 1]));
							else
								printf("Line %s:    %s", $__exc_IntLine, htmlentities(QErrorHandler::$FileLinesArray[$__exc_IntLine - 1]));
						}
						print('</pre>');
						unset($__exc_IntLine);
?>
			</div><br />

<?php
			if (isset(QErrorHandler::$ErrorAttributeArray)) {
				foreach (QErrorHandler::$ErrorAttributeArray as $__exc_ObjErrorAttribute) {
					printf("<b>%s:</b>&nbsp;&nbsp;", $__exc_ObjErrorAttribute->Label);
					$__exc_StrJavascriptLabel = str_replace(" ", "", $__exc_ObjErrorAttribute->Label);
					if ($__exc_ObjErrorAttribute->MultiLine) {
						printf("\n<a href=\"javascript:ToggleHidden('%s')\">Show/Hide</a>",
							$__exc_StrJavascriptLabel);
						printf('<br /><br /><div id="%s" class="code" style="Display: none;"><pre>%s</pre></div><br />',
							$__exc_StrJavascriptLabel,
							htmlentities($__exc_ObjErrorAttribute->Contents));
					} else
						printf("%s\n<br /><br />\n", htmlentities($__exc_ObjErrorAttribute->Contents));
				}
				unset($__exc_StrJavascriptLabel);
				unset($__exc_ObjErrorAttribute);
			}
?>

			<b>Call Stack:</b>
			<br><br>
			<div class="code">
				<pre><?php print(QErrorHandler::$StackTrace); ?></pre>
			</div><br />

			<b>Global Variables Dump:</b>&nbsp;&nbsp;
			<a href="javascript:ToggleHidden('VariableDump')">Show/Hide</a>
			<br /><br />
			<div id="VariableDump" class="code" style="Display: none;">
<?php
				// Dump All Variables
				foreach ($GLOBALS as $__exc_Key => $__exc_Value) {
					if (isset($__exc_Key)) global $$__exc_Key;
				}

				$__exc_ObjVariableArray = get_defined_vars();
				if (\Qcodo\Handlers\WebService::$HttpRequest) $__exc_ObjVariableArray['WebService_HttpRequest'] = \Qcodo\Handlers\WebService::$HttpRequest;
				$__exc_ObjVariableArrayKeys = array_keys($__exc_ObjVariableArray);
				sort($__exc_ObjVariableArrayKeys);

				$__exc_StrToDisplay = "";
				$__exc_StrToScript = "";
				foreach ($__exc_ObjVariableArrayKeys as $__exc_Key) {
					if ((strpos($__exc_Key, "__exc_") === false) && (strpos($__exc_Key, "_DATE_") === false) && ($__exc_Key != "GLOBALS")) {
						try {
							if (($__exc_Key == 'HTTP_SESSION_VARS') || ($__exc_Key == '_SESSION')) {
								$__exc_ObjSessionVarArray = array();
								foreach ($$__exc_Key as $__exc_StrSessionKey => $__exc_StrSessionValue) {
									if (strpos($__exc_StrSessionKey, 'qform') !== 0)
										$__exc_ObjSessionVarArray[$__exc_StrSessionKey] = $__exc_StrSessionValue;
								}
								$__exc_StrVarExport = htmlentities(var_export($__exc_ObjSessionVarArray, true));
							} else
								$__exc_StrVarExport = QErrorHandler::VarExport($__exc_ObjVariableArray[$__exc_Key]);

							$__exc_StrToDisplay .= sprintf("  <a href=\"#\" onclick=\"RenderPage(%s); return false;\" title=\"%s\">%s</a>\n", $__exc_Key, $__exc_StrVarExport, $__exc_Key);
							$__exc_StrToScript .= sprintf("  const %s = \"<pre>%s</pre>\";\n", $__exc_Key, QErrorHandler::PrepDataForScript($__exc_StrVarExport));
						} catch (Exception $__exc_objExcOnVarDump) {
							$__exc_StrToDisplay .= sprintf("  Fatal error:  Nesting level too deep - recursive dependency?\n", $__exc_objExcOnVarDump->Message);
						}
					}
				}

                printf('<script type="text/javascript">%s</script>', $__exc_StrToScript);
                print('<pre>');
				print($__exc_StrToDisplay . '</pre>');
?>
			</div><br />
			<hr width="100%" size="1" color="#dddddd" />
			<center><em>
				<?php print(QErrorHandler::$Type); ?> Report Generated:&nbsp;&nbsp;<?php print(QErrorHandler::$DateTimeOfError); ?>
				<br/>
<?php if (QErrorHandler::$FileNameOfError) { ?>
				<?php print(QErrorHandler::$Type); ?> Report Logged:&nbsp;&nbsp;<?php print(QErrorHandler::$FileNameOfError); ?>
<?php } else { ?>
				<?php print(QErrorHandler::$Type); ?> Report NOT Logged
<?php } ?>
			</em></center>
	</div>
	</body>
</html>

<?php if (QErrorHandler::$FileNameOfError) { ?>
<!--qcodo--<error valid="true">
<type><?php print(QErrorHandler::$Type); ?></type>
<title><?php print(QErrorHandler::$Message); ?></title>
<datetime><?php print(QErrorHandler::$DateTimeOfError); ?></datetime>
<isoDateTime><?php print(QErrorHandler::$IsoDateTimeOfError); ?></isoDateTime>
<filename><?php print(QErrorHandler::$FileNameOfError); ?></filename>
<script><?php print($_SERVER["PHP_SELF"]); ?></script>
<server><?php print($_SERVER['SERVER_NAME']); ?></server>
<agent><?php if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) print($_SERVER['HTTP_USER_AGENT']); ?></agent>
</error>-->
<?php } ?>
