<!DOCTYPE html>
<html>
<head>
	<title>PHP <?php _p(QErrorHandler::$Type); ?> - <?php _p(QErrorHandler::$Message); ?></title>
	<style type="text/css">
	body { 
		font-family: Arial, Helvetica, sans-serif;
		font-size: 14px;
		margin: 0;
	}
	a { color: #009721;}
	a:link, a:visited { text-decoration: none; }
	a:hover { text-decoration: underline; }
	form { margin: 0; }
	
	#container { }
	#content { padding: 15px 20px 0 20px; margin-bottom: 20px; }
	
	#headerContainer {
		overflow: hidden;
		padding-bottom: 5px;
		line-height: 18px;
		width: 100%;
	}
	#headerBorder { border-bottom: 1px solid #c9f991; }
	#header {
		background-color: #2baa28;
		color: #ffffff;
		border-top: 1px solid #c1e2ac;
		border-bottom: 1px solid #c1e2ac;
		font-size: 16px;
		overflow: hidden;
		height: 100%;
		
		-webkit-box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.25);
		-moz-box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.25);
		box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.25);
		text-shadow: 1px 1px 1px rgba(0, 50, 11, 0.5);
	}
		#header a { color: #fff; }
	
	/* diffrent header do error page */
	.headerError {
		background-color: #a00 !important;
		border-top: 1px solid #e00 !important;
		border-bottom: 1px solid #e00 !important;
	}
	.headerErrorBorder {
		border-bottom: 1px solid #900 !important;
	}
	#header #hleft {
		float: left;
		padding: 10px 0px 10px 20px;
		line-height: 24px;
	}
	#header .hbig {	
		font-size: 21px;
		font-weight: bold;
		text-shadow: 1px 1px 2px rgba(0, 50, 11, 0.7);
	}
	#header .hsmall { font-size: 14px; }
	#header #hright {
		float: right;
		font-size: 13px;
		padding: 10px 10px 10px 0;
		text-align: right;
	}
	
	.title {
		font-size: 18px;
		font-weight: bold;
	}
	.code_title { 
		font-size: 16px; 
		font-weight: bold; 
	}
	
	pre { 
		font-family: 'Lucida Console', 'Courier New', Courier, monospaced;
		font-size: 14px;
		line-height: 16px;
		margin: 10px 0;
	}	
	pre code {
		font-size: 12px;
		margin: 0;
		padding: 10px;
		display: block;

		background-color: #f0f0f0;
		border: 1px solid #d8d8d8;
		-moz-border-radius: 5px;
		-webkit-border-radius: 5px;
		border-radius: 5px;
		-webkit-box-shadow: 0 0 6px rgba(0, 0, 0, 0.25);
		-moz-box-shadow: 0 0 6px rgba(0, 0, 0, 0.25);
		box-shadow: 0 0 6px rgba(0, 0, 0, 0.25);
	}
		
	.function { 
		font-weight: bold;
		line-height: 21px;
	}
	.function_details {
		color: #444;
		font-size: 14px;
		line-height: 21px;
	}

	a.smallbutton {
		border: 1px solid #aaa;
		background-color: #f6f6f6;
		color: #000;
		padding: 0px 4px;
		-moz-border-radius: 2px;
		-webkit-border-radius: 2px;
		border-radius: 2px;
		font-size: 14px;
	}
		a.smallbutton:hover { background-color: #c1e2ac; border-color: #69ca32; outline: 0; text-decoration: none; }
	
	p.buttons { padding: 5px 0; margin: 10px 0; }
		p.buttons a {
			font-family: inherit;
			font-size: inherit;
			border: 1px solid #aaa;
			background-color: #f6f6f6;
			color: #000;

			font-weight: bold;
			padding: 4px 8px;
			-moz-border-radius: 2px;
			-webkit-border-radius: 2px;
			border-radius: 2px;
		}
			p.buttons a:hover { background-color: #c1e2ac; border-color: #69ca32; outline: 0; text-decoration: none; }

	.time { font-size: 14px; }
	.slow, .error { color: #F00; }
	.clear {
		clear: both;
		font-size: 0pt;
	}
	
	#footer {
		border-top: 1px solid #d8d8d8;
		color: #444;
		font-size: 12px;
		text-align: center;
		padding-top: 20px;
		margin: 0 20px;
		clear: both;
	}		
	</style>
	<script type="text/javascript">
		function RenderPage(strHtml) {
			document.rendered.strHtml.value = strHtml;
			document.rendered.submit();
			return false;
		}
		
		function Toggle(strWhatId, strButtonId) {
			var obj = document.getElementById(strWhatId);
			var objButton = document.getElementById(strButtonId);
			
			if (obj && objButton) {
				if (obj.style.display == "block") {
					obj.style.display = "none";
					objButton.innerHTML = "Show";
				}
				else { 
					obj.style.display = "block";
					objButton.innerHTML = "Hide";
				}
			}
			return false;
		}
	</script>
</head>
<body>
	<div id="container">
		<div id="headerContainer">
			<div id="headerBorder" class="headerErrorBorder">
				<div id="header" class="headerError">
					<div id="hleft">
						<span class="hsmall"><?php _p(QErrorHandler::$Type); ?> in PHP Script</span><br />
						<span class="hbig"><?php _p($_SERVER["PHP_SELF"]); ?></span>
					</div>
					<div id="hright">
						<b>PHP Version:</b> <?php _p(PHP_VERSION); ?>&nbsp;&nbsp;
						<b>Server Name:</b> <?php _p($_SERVER['SERVER_NAME']); ?>&nbsp;&nbsp;
						<b>Qcodo Version:</b> <?php _p(QCODO_VERSION); ?>
						<br />
						<?php if (array_key_exists('OS', $_SERVER)) printf('<b>Operating System:</b> %s;&nbsp;&nbsp;', $_SERVER['OS']); ?>
						<b>Application:</b> <?php _p($_SERVER['SERVER_SOFTWARE']); ?>
						<br/>
						<b>HTTP User Agent:</b> <?php _p($_SERVER['HTTP_USER_AGENT']); ?>
					</div>
					<br class="clear"/>
				</div>
			</div>
		</div>
		
		<div id="content">
			<span span="title"><?php _p(QErrorHandler::$MessageBody, false); ?></span>
			<form method="post" action="<?php _p(__VIRTUAL_DIRECTORY__ . __PHP_ASSETS__) ;?>/_core/error_already_rendered_page.php" target="blank" name="rendered">
				<input type="hidden" name="strHtml" value="">
			</form>
			<br/>
			<b><?php _p(QErrorHandler::$Type); ?> Type:</b>&nbsp;&nbsp;
			<?php _p(QErrorHandler::$ObjectType); ?>
			<br/>
			<br/>
<?php
			if (isset(QErrorHandler::$RenderedPage)) {
?>
				<script type="text/javascript">var RenderedPage = "<?php _p(QErrorHandler::PrepDataForScript(QErrorHandler::$RenderedPage), false); ?>";</script>
				<b>Rendered Page:</b>&nbsp;&nbsp;
				<a href="#" onClick="return RenderPage(RenderedPage)">Click here</a> to view contents able to be rendered
				<br/><br/>
<?php
			}
?>
			<b>Source File:</b>&nbsp;&nbsp;<?php _p(QErrorHandler::$Filename); ?>&nbsp;&nbsp;&nbsp;&nbsp;
			<b>Line:</b>&nbsp;&nbsp;<?php _p(QErrorHandler::$LineNumber); ?>
			<br/>
			
			<pre><code><?php
				for ($__exc_IntLine = max(1, QErrorHandler::$LineNumber - 5); $__exc_IntLine <= min(count(QErrorHandler::$FileLinesArray), QErrorHandler::$LineNumber + 5); $__exc_IntLine++) {
					if (QErrorHandler::$LineNumber == $__exc_IntLine)
						printf("<span class=\"error\">Line %s:    %s</span>", $__exc_IntLine, htmlentities(QErrorHandler::$FileLinesArray[$__exc_IntLine - 1]));
					else
						printf("Line %s:    %s", $__exc_IntLine, htmlentities(QErrorHandler::$FileLinesArray[$__exc_IntLine - 1]));
				}
				unset($__exc_IntLine);
			?></code></pre>
<?php
			if (isset(QErrorHandler::$ErrorAttributeArray)) {
				foreach (QErrorHandler::$ErrorAttributeArray as $__exc_ObjErrorAttribute) {
					printf("<b>%s:</b>&nbsp;&nbsp;", $__exc_ObjErrorAttribute->Label);
					$__exc_StrJavascriptLabel = str_replace(" ", "", $__exc_ObjErrorAttribute->Label);
					if ($__exc_ObjErrorAttribute->MultiLine) {
						printf("\n<a id=\"button%s\" href=\"#\" onClick=\"return Toggle('%s', 'button%s')\">Show</a>", 
							$__exc_StrJavascriptLabel,
							$__exc_StrJavascriptLabel,
							$__exc_StrJavascriptLabel);
						printf('<br /><br /><div id="%s" class="code" style="display: none;"><pre>%s</pre></div><br />', 
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
			<br/>
			<pre><code><?php _p(QErrorHandler::$StackTrace); ?></code></pre>

			<b>Global Variables Dump:</b>&nbsp;&nbsp;

			<a id="button" href="#" onClick="return Toggle('VariableDump', 'button')" class="smallbutton">Show</a>
			<br/>
			<pre id="VariableDump" style="display: none;"><code><?php

			// Dump All Variables
			foreach ($GLOBALS as $__exc_Key => $__exc_Value) {
				if (isset($__exc_Key)) global $$__exc_Key;
			}

			$__exc_ObjVariableArray = get_defined_vars();
			$__exc_ObjVariableArrayKeys = array_keys($__exc_ObjVariableArray);
			sort($__exc_ObjVariableArrayKeys);

			$__exc_StrToDisplay = "";
			$__exc_StrToScript = "";
			foreach ($__exc_ObjVariableArrayKeys as $__exc_Key) {
				if ((strpos($__exc_Key,  "__exc_") === false)  && (strpos($__exc_Key, "_DATE_")  === false)  && ($__exc_Key != "GLOBALS")) {
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

						$__exc_StrToDisplay .= sprintf("<a href=\"#\" onClick=\"return RenderPage(%s)\" title=\"%s\">%s</a>\n", $__exc_Key, $__exc_StrVarExport, $__exc_Key);
						$__exc_StrToScript .= sprintf("  %s = \"<pre>%s</pre>\";\n", $__exc_Key, QErrorHandler::PrepDataForScript($__exc_StrVarExport));
					} catch (Exception $__exc_objExcOnVarDump) {
						$__exc_StrToDisplay .= sprintf("  Fatal error:  Nesting level too deep - recursive dependency?\n", $__exc_objExcOnVarDump->Message);
					}
				}
			}

			_p($__exc_StrToDisplay, false);
			printf('</code></pre>');
			printf('<script type="text/javascript">%s</script>', $__exc_StrToScript);
?>
			</div>
			<div id="footer">
				<?php _p(QErrorHandler::$Type); ?> Report Generated:&nbsp;&nbsp;<b><?php _p(QErrorHandler::$IsoDateTimeOfError); ?></b>
				<br/>
				<?php if (QErrorHandler::$FileNameOfError) { ?>
					<?php _p(QErrorHandler::$Type); ?> Report Logged:&nbsp;&nbsp;<b><?php _p(QErrorHandler::$FileNameOfError); ?></b>
				<?php } else { ?>
					<?php _p(QErrorHandler::$Type); ?> Report <b>NOT Logged</b>
				<?php } ?>
			</div>
		</div>
	</body>
</html>

<?php if (QErrorHandler::$FileNameOfError) { ?>
<!--qcodo--<error valid="true">
<type><?php _p(QErrorHandler::$Type); ?></type>
<title><?php _p(QErrorHandler::$Message); ?></title>
<datetime><?php _p(QErrorHandler::$DateTimeOfError); ?></datetime>
<isoDateTime><?php _p(QErrorHandler::$IsoDateTimeOfError); ?></isoDateTime>
<filename><?php _p(QErrorHandler::$FileNameOfError); ?></filename>
<script><?php _p($_SERVER["PHP_SELF"]); ?></script>
<server><?php _p($_SERVER['SERVER_NAME']); ?></server>
<agent><?php _p($_SERVER['HTTP_USER_AGENT']); ?></agent>
</error>-->
<?php } ?>