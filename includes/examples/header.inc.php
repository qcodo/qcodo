<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php _p(QApplication::$EncodingType); ?>" />
		<title>Qcodo Development Framework - Examples</title>
		<link rel="stylesheet" type="text/css" href="<?php _p(__VIRTUAL_DIRECTORY__ . __CSS_ASSETS__ . '/qcontrols.css'); ?>"></link>
		<link rel="stylesheet" type="text/css" href="<?php _p(__VIRTUAL_DIRECTORY__ . __CSS_ASSETS__ . '/examples/examples.css'); ?>"></link>
		<script type="text/javascript" src="<?php _p(__VIRTUAL_DIRECTORY__ . __JS_ASSETS__ . '/_core/_qc_packed.js'); ?>"></script>
		<script type="text/javascript">
			function ViewSource(intCategoryId, intExampleId) {
				var objWindow = window.open("../view_source.php/" + intCategoryId + "/" + intExampleId, "ViewSource", "menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=1000,height=750,left=50,top=50");
				objWindow.focus();
			}
		</script>
	</head>
	<body> 

	<table border="0" cellspacing="0" width="100%">
		<tr>
			<td class="headingLeft"><span class="headingLeftSmall">
				<?php _p((Examples::GetCategoryId() + 1) . '. ' . Examples::$Categories[Examples::GetCategoryId()]['name'], false); ?><br/>
			</span>
				<?php _p(Examples::PageName(), false); ?><br/>
			<span class="headingLeftSmall">
				<?php _p(Examples::PageLinks(), false); ?>
			</span>
			</td>
			<td class="headingRight"><br/>
				<b><a href="javascript:ViewSource(<?php _p(Examples::GetCategoryId() . ',' . Examples::GetExampleId()); ?>);" class="headingLink">View Source</a></b>
				<br/>
				<span class="headingLeftSmall">will open in a new window</span>
			</td>
		</tr>
	</table>
	
	<div class="page">