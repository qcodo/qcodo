<?php require(dirname(__FILE__) . '/../_require_prepend.inc.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Qcodo Development Framework - Code Generator</title>
	<style type="text/css">@import url("<?php _p(__VIRTUAL_DIRECTORY__ . __CSS_ASSETS__); ?>/_core/corepage.css");</style>
</head>
<body>
	<div id="container">
		<div id="headerContainer">
			<div id="headerBorder">
				<div id="header">
					<div id="hleft">
						<span class="hsmall">Qcodo Development Framework <?php _p(QCODO_VERSION); ?></span><br/>
						<span class="hbig">Code Generator</span>
					</div>
					<div id="hright">
						<b>PHP Version:</b> <?php _p(PHP_VERSION); ?>&nbsp;&nbsp;
						<b>Server Name:</b> <?php _p($_SERVER['SERVER_NAME']); ?>&nbsp;&nbsp;
						<b>Qcodo Version:</b> <?php _p(QCODO_VERSION); ?>
						<br/>
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
<?php
	// Security check for ALLOW_REMOTE_ADMIN
	// To allow access REGARDLESS of ALLOW_REMOTE_ADMIN, simply remove the line below
	QApplication::CheckRemoteAdmin();

	/////////////////////////////////////////////////////
	// Run CodeGen, using the path to the codegen settings "settings.xml" file
	// Change below if your settings.xml file is in a different location
	/////////////////////////////////////////////////////
	$strSettingsXmlFilePath = __DEVTOOLS_CLI__ . '/settings/codegen.xml';

	/*if (!file_exists($strSettingsXmlFilePath) || !is_file($strSettingsXmlFilePath)) {
		_p('<div class="error">', false);
		_p('The Qcodo Web-based CodeGen tool expects the codegen settings <strong>"codegen.xml"</strong> file to be located at:<br/><strong>', false);
		_p($strSettingsXmlFilePath);
		_p('</strong><br/><br/>', false);
		_p('Either ensure that the <strong>"codegen.xml"</strong> exists there (recommended) or alternatively to update:<br/>', false);
		_p('<strong>' . __FILE__ . '</strong><br/> to reflect the correct path (not as recommended).</pre>', false);
		_p('</div></body></html>', false);
		exit();
	}*/

	QCodeGen::Run($strSettingsXmlFilePath);

	function DisplayMonospacedText($strText) {
		$strText = QApplication::HtmlEntities($strText);
		$strText = str_replace('	', '    ', $strText);
		$strText = str_replace(' ', '&nbsp;', $strText);
		$strText = str_replace("\r", '', $strText);
		$strText = str_replace("\n", '<br/>', $strText);

		_p($strText, false);
	}

	//////////////////
	// Output the Page
	//////////////////
?>
		<?php if ($strErrors = QCodeGen::$RootErrors) { ?>
			<p><b>The following root errors were reported:</b></p>
			<pre class="error"><code><?php DisplayMonospacedText($strErrors); ?></code></pre>
			<p></p>
		<?php } else { ?>
			<p><b>CodeGen Settings (as evaluated from <?php _p(QCodeGen::$SettingsFilePath); ?>):</b></p>
			<pre><code><?php DisplayMonospacedText(QCodeGen::GetSettingsXml()); ?></code></pre>
			<p></p>
		<?php } ?>

		<?php foreach (QCodeGen::$CodeGenArray as $objCodeGen) { ?>
			<p><b><?php _p($objCodeGen->GetTitle()); ?></b></p>
			<span class="code_title"><?php _p($objCodeGen->GetReportLabel()); ?></span>
			<pre><code><?php DisplayMonospacedText($objCodeGen->GenerateAll()); ?></code></pre>
			
			<?php if ($strErrors = $objCodeGen->Errors) { ?>
			<p class="code_title">The following errors were reported:</p>
			<pre class="error"><code><?php DisplayMonospacedText($objCodeGen->Errors); ?></code></pre>
			<?php } ?>
		
		<?php } ?>
		
		<?php foreach (QCodeGen::GenerateAggregate() as $strMessage) { ?>
			<p><b><?php _p($strMessage); ?></b></p>
		<?php } ?>

		</div>
		<div id="footer">
			<b>Code Generated:</b> <?php _p(date('l, F j Y, g:i:s A')); ?>
		</div>
	</div>
</body>
</html>