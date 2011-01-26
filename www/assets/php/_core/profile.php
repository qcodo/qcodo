<?php
	require(dirname(__FILE__) . '/../_require_prepend.inc.php');
	
	//Exit gracefully if called directly or profiling data is missing.
	if ( !isset($_POST['intDatabaseIndex']) && !isset($_POST['strProfileData']) && !isset($_POST['strReferrer']) )
		exit('Nothing to profile. No Database Profiling data recived.');

	if ( !isset($_POST['intDatabaseIndex']) || !isset($_POST['strProfileData']) || !isset($_POST['strReferrer']) )
		throw new Exception('Database Profiling data appears to have been corrupted.');
	
	$intDatabaseIndex = $_POST['intDatabaseIndex'];
	$strProfileData = $_POST['strProfileData'];
	$strReferrer = $_POST['strReferrer'];

	$objProfileArray = unserialize(base64_decode($strProfileData));
	$objProfileArray = QType::Cast($objProfileArray, QType::ArrayType);
	if ((count($objProfileArray) % 2) != 0)
		throw new Exception('Database Profiling data appears to have been corrupted.');
		
	$intCount = count($objProfileArray);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Qcodo Development Framework - Database Profiling Tool</title>
	<style type="text/css">@import url("<?php _p(__VIRTUAL_DIRECTORY__ . __CSS_ASSETS__); ?>/_core/corepage.css");</style>
	<script type="text/javascript">
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

		function ShowAll() {
			for (var intIndex = 1; intIndex <= <?php _p($intCount); ?>; intIndex = intIndex + 2) {
				var objQuery = document.getElementById('query' + intIndex);
				var objButton = document.getElementById('button' + intIndex);
				objQuery.style.display = "block";
				objButton.innerHTML = "Hide";
			}
			return false;
		}

		function HideAll() {
			for (var intIndex = 1; intIndex <= <?php _p($intCount); ?>; intIndex = intIndex + 2) {
				var objQuery = document.getElementById('query' + intIndex);
				var objButton = document.getElementById('button' + intIndex);
				objQuery.style.display = "none";
				objButton.innerHTML = "Show";
			}
			return false;
		}
	</script>
</head>
<body>
	<div id="container">
		<div id="headerContainer">
			<div id="headerBorder">
				<div id="header">
					<div id="hleft">
						<span class="hsmall">Qcodo Development Framework <?php echo QCODO_VERSION ?></span><br/>
						<span class="hbig">Database Profiling Tool</span>
					</div>
					<div id="hright">
						<b>Database Index:</b> <?php _p($intDatabaseIndex); ?>&nbsp;&nbsp;
						<b>Database Type:</b> <?php _p(QApplication::$Database[$intDatabaseIndex]->Adapter); ?><br/>
						<b>Database Server:</b> <?php _p(QApplication::$Database[$intDatabaseIndex]->Server); ?>&nbsp;&nbsp;
						<b>Database Name:</b> <?php _p(QApplication::$Database[$intDatabaseIndex]->Database); ?><br/>
						<b>Profile Generated From:</b> <?php _p($strReferrer); ?>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>

	<div id="content">
		<span class="title">
<?php
		$intCount = $intCount / 2;
		
		switch ($intCount) {
			case 0: _p('<b>There were no queries that were performed.</b>', false); break;
			case 1: _p('<b>There was 1 query that was performed.</b>', false); break;
			default: printf('<b>There were %s queries that were performed.</b>', $intCount); break;
		};
?>
		</span>
		<br/>
		<br/>
		<a href="#" onClick="return ShowAll()" class="smallbutton">Show All</a>
		<a href="#" onClick="return HideAll()" class="smallbutton">Hide All</a>
		<br/>
		<br/>
<?php
			for ($intIndex = 0; $intIndex < count($objProfileArray); $intIndex++) {
				if ((count($objProfileArray[$intIndex]) > 3) &&
					(array_key_exists('function', $objProfileArray[$intIndex][2])) &&
					(($objProfileArray[$intIndex][2]['function'] == 'QueryArray') ||
					 ($objProfileArray[$intIndex][2]['function'] == 'QuerySingle') ||
					 ($objProfileArray[$intIndex][2]['function'] == 'QueryCount')))
					$objDebugBacktrace = $objProfileArray[$intIndex][3];
				else
					$objDebugBacktrace = $objProfileArray[$intIndex][2];
				$intIndex++;
				$strQuery = $objProfileArray[$intIndex];

				$objArgs = (array_key_exists('args', $objDebugBacktrace)) ? $objDebugBacktrace['args'] : array();
				$strClass = (array_key_exists('class', $objDebugBacktrace)) ? $objDebugBacktrace['class'] : null;
				$strType = (array_key_exists('type', $objDebugBacktrace)) ? $objDebugBacktrace['type'] : null;
				$strFunction = (array_key_exists('function', $objDebugBacktrace)) ? $objDebugBacktrace['function'] : null;
				$strFile = (array_key_exists('file', $objDebugBacktrace)) ? $objDebugBacktrace['file'] : null;
				$strLine = (array_key_exists('line', $objDebugBacktrace)) ? $objDebugBacktrace['line'] : null;
?>
			<span class="function">
				Called by <?php _p($strClass . $strType . $strFunction . '(' . implode(', ', $objArgs) . ')'); ?>
				<a href="#" onClick="return Toggle('query<?php _p($intIndex); ?>', 'button<?php _p($intIndex); ?>')" id="button<?php _p($intIndex); ?>" class="smallbutton">
					Show
				</a>
			</span>&nbsp;&nbsp;<br/>
			<span class="function_details">
				<b>File: </b><?php _p($strFile); ?>; &nbsp;&nbsp;<b>Line: </b><?php _p($strLine); ?>
			</span>
			<pre id="query<?php _p($intIndex); ?>" style="display: none"><code><?php _p($strQuery); ?></code></pre>
			<br/>
			<br/>
<?php
		}
?>			</div>
	</div>
	<script>
		if (<?php _p($intCount); ?> <= 5) ShowAll();
	</script>
</body>
</html>