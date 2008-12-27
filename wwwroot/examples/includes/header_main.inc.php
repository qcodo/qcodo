<?php require('includes/examples.inc.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php _p(QApplication::$EncodingType); ?>" />
		<title>Qcodo Development Framework - Examples</title>
		<link rel="stylesheet" type="text/css" href="<?php _p(__VIRTUAL_DIRECTORY__ . __EXAMPLES__ . '/includes/examples.css'); ?>"></link>
	</head>
	<body> 

	<table border="0" cellspacing="0" width="100%">
		<tr>
			<td class="headingLeft"><span class="headingLeftSmall">Qcodo Examples - <?php _p(QCODO_VERSION); ?><br/></span>
				<?php _p(Examples::PageName(), false); ?><br/>
			<span class="headingLeftSmall"></span>
			</td>
			<td class="headingRight"></td>
		</tr>
	</table>
	
	<div class="page">