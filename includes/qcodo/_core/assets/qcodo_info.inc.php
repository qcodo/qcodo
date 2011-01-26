<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>Qcodo Development Framework - Start Page</title>
	<style type="text/css">@import url("<?php _p(__VIRTUAL_DIRECTORY__ . __CSS_ASSETS__); ?>/_core/corepage.css");</style>
</head>
<body>
	<div id="container">
		<div id="headerContainer">
			<div id="headerBorder">
				<div id="header">
					<div id="hleft">
						<span class="hsmall">Qcodo Development Framework <?php echo QCODO_VERSION ?></span><br/>
						<span class="hbig">Start Page</span>
					</div>
					<br class="clear"/>
				</div>
			</div>
		</div>

		<div id="content">
			<span id="title">It worked!</span><br/>
			<br/>
			<b>If you are seeing this, then it means that the framework has been successfully installed.</b><br/>
			<br/>
			Make sure your database connection properties are up to date, and then you can add tables to your database.
			To codegen, you will want to run the codegen using the qcodo codegen CLI tool from the command-line:<br/>
			<pre><code>$ <?php _p(__DEVTOOLS_CLI__); ?>/qcodo codegen --help</code></pre>
			Or alternatively you can use web based codegen available at:
			<ul>
				<li>
					<a href="<?php _p(__PHP_ASSETS__ ) ?>/_core/codegen.php">
						<?php _p(__PHP_ASSETS__ ) ?>/_core/codegen.php
					</a>
				</li>
			</ul>
			After codegenning, you can use either of the following tools to view the "generated" draft pages of your database application:
			<ul>
				<li>
					<a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?>/index.php">
						<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?>
					</a>
					- to view the generated Form Drafts of your database
				</li>
				<li>
					<a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?>/dashboard/index.php">
						<?php _p(__VIRTUAL_DIRECTORY__ . __PANEL_DRAFTS__) ?>
					</a>
					- to view the generated Panel Drafts "dashboard" of your database
				</li>
			</ul>
			
			<?php if( file_exists('examples')) { ?>
			Qcodo examples. Some of examples require configured database, see /includes/examples/database for info how to set database properly.
			<ul>
				<li>
					<a href="<?php _p(__VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__) ?>/examples/index.php">
						<?php _p(__VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__) ?>/examples/
					</a>
					- to run the Qcodo Examples Site locally
				</li>
			</ul>
			<?php } ?>
			
			For more information, please go to the Qcodo website at: <a href="http://www.qcodo.com/">http://www.qcodo.com/</a><br/>
			<br/>
			<?php QApplication::VarDump(); ?>
		</div>
	</div>
</body>
</html>