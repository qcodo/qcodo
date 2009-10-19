<?php
	// Include prepend.inc to load Qcodo
	require('includes/prepend.inc.php');	/* if you DO NOT have "includes/" in your include_path */
	// require('prepend.inc');				/* if you DO have "includes/" in your include_path */
?>
<html>
	<head>
		<title>Qcodo Development Framework - Start Page</title>
		<style>
			TD, BODY { font: 12px <?php _p(QFontFamily::Verdana) ?>; }
			.title { font: 30px <?php _p(QFontFamily::Verdana) ?>; font-weight: bold; margin-left: -2px;}
			.title_action { font: 12px <?php _p(QFontFamily::Verdana) ?>; font-weight: bold; margin-bottom: -4px; }
			.item_divider { line-height: 16px; }
			.heading { font: 16px <?php _p(QFontFamily::Verdana) ?>; font-weight: bold; }
		</style>
	</head><body>	
		<div class="title_action">Qcodo Development Framework <?= QCODO_VERSION ?></div>
		<div class="title">Start Page</div>
		<br class="item_divider" />

		<span class="heading">It worked!</span><br /><br />

		<b> If you are seeing this, then it means that the framework has been successfully installed.</b>
		<br /><br /><br />
		
		Make sure your database connection properties are up to date, and then you can add tables to your database, and go to either of the following webpages:<br />
		
		<ul>
		<li><a href="<?php _p(__VIRTUAL_DIRECTORY__ . __DEVTOOLS__) ?>/codegen.php"><?php _p(__VIRTUAL_DIRECTORY__ . __DEVTOOLS__) ?>/codegen.php</a> - to code generate your tables</li>
		<li><a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?>/index.php"><?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?></a> - to view the generated Form Drafts of your database</li>
		<li><a href="<?php _p(__VIRTUAL_DIRECTORY__ . __EXAMPLES__) ?>/index.php"><?php _p(__VIRTUAL_DIRECTORY__ . __EXAMPLES__) ?></a> - to run the Qcodo Examples Site locally</li>
		</ul>
		
		For more information, please go to the Qcodo website at: <a href="http://www.qcodo.com/">http://www.qcodo.com/</a>
		<br /><br /><br />

		<?php QApplication::VarDump(); ?>
	</body>
</html>