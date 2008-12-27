<?php
	require('../includes/prepend.inc.php');
	require('includes/header_main.inc.php');
?>
<div style="text-align: center; width: 100%;">
	<div style="text-align: left; margin: 0px auto 0px auto; width: 830px;">

		<div class="instructions_main" style="width: 770px;">
			<div class="instruction_title">Qcodo Examples Site</div>
			
			This is a collection of many small examples that demonstrate the functionality
			in Qcodo.  Later examples tend to build upon functionality or concepts that are
			discussed in prior ones, which allows the Examples site to be viewed as a quasi-tutorial.
			However, you should still feel free to check out any of the examples as you wish.<br/><br/>
			
			The Examples are broken into three main parts: the <b>Code Generator</b>, the <b>QForm and QControl Library</b>, and
			<b>Other Qcodo Functionality</b>.<br/><br/>

			<span style="font-size: 10px;">* Some of the examples (marked with a "*") use the <b>Examples Site Database</b>.
			This database (which consists of six tables and some preloaded sample data) is included in the Examples Site directories.  See 
			<a href="<?php _p(__VIRTUAL_DIRECTORY__ . __EXAMPLES__); ?>/code_generator/intro.php" class="bodyLink" style="font-weight: bold;">Basic CodeGen &gt; About the Database</a>
			for more information.
		</span>
		</div>
		
		<script type="text/javascript">
			function DisplayPart(strPartId) {
				switch (strPartId) {
					case "1":
						document.getElementById("part1").style.display = "block";
						document.getElementById("part2").style.display = "none";
						document.getElementById("part3").style.display = "none";

						document.getElementById("link1").className = "main_navselected";
						document.getElementById("link2").className = "main_navlink";
						document.getElementById("link3").className = "main_navlink";
						break;
					case "2":
						document.getElementById("part1").style.display = "none";
						document.getElementById("part2").style.display = "block";
						document.getElementById("part3").style.display = "none";
						
						document.getElementById("link1").className = "main_navlink";
						document.getElementById("link2").className = "main_navselected";
						document.getElementById("link3").className = "main_navlink";
						break;
					case "3":
						document.getElementById("part1").style.display = "none";
						document.getElementById("part2").style.display = "none";
						document.getElementById("part3").style.display = "block";
						
						document.getElementById("link1").className = "main_navlink";
						document.getElementById("link2").className = "main_navlink";
						document.getElementById("link3").className = "main_navselected";
						break;
				}
			}
		</script>
		
		<div style="margin-left: 20px">
		
		<div class="main_navigator">
		<a id="link1" href="javascript:DisplayPart('1')" class="main_navselected">The Code Generator</a>
		 &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; 
		<a id="link2" href="javascript:DisplayPart('2')" class="main_navlink">The QForm and QControl Library</a>
		 &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; 
		<a id="link3" href="javascript:DisplayPart('3')" class="main_navlink">Other Qcodo Functionality</a>
		</div>

<?php
			for ($intIndex = 0; $intIndex < count(Examples::$Categories); $intIndex++) {
				$objExampleCategory = Examples::$Categories[$intIndex];

				if ($intIndex == 0) {
?>
					<div id="part1">
					<div class="main_info">
					<b>The Code Generator</b> is at the heart of the Model in the MVC (Model, View, Controller) architecture.
					It uses the data model you have defined to create all your data objects, relationships and CRUD
					functionality.<br/><br/>
					
					Sections 1 - 3 look specifically at the <b>Code Generator</b>, the <b>Object Relational Model</b> it creates, and the
					<b>Qcodo Query</b> library which powers it.</div>
					<blockquote>
<?php
				}

				if ($intIndex == 3) {
?>
					</blockquote></div>
					<div id="part2" style="display: none;">
					<div class="main_info">
					QForms is a <b>stateful, event-driven architecture for web-based forms</b>, providing the display and
					presentation functionality for Qcodo.  Basically, it is your "V" and "C" of the MVC architecture.<br/><br/>
		
					Sections 4 - 10 are examples on how to use the <b>QForm</b> and <b>QControl</b> libraries
					within the Qcodo Development Framework.</div>
					<blockquote>
<?php
				}

				if ($intIndex == 10) {
?>
					</blockquote></div>
					<div id="part3" style="display: none;">
					<div class="main_info">
					Beyond the <b>Code Generator</b> and the <b>Qform Library</b>, Qcodo also many other modules and features
					that is useful for web application developers.</div>

					<blockquote>
<?php
				}

				printf('<p>%s. <b>%s</b> - %s</p><ul>', ($intIndex + 1), $objExampleCategory['name'], $objExampleCategory['description']);
				
				foreach ($objExampleCategory as $strKey => $strValue) {
					if (is_numeric($strKey)) {
						$intPosition = strpos($strValue, ' ');
						printf('<li><a href="%s%s" class="bodyLink">%s</a></li>', __VIRTUAL_DIRECTORY__ . __EXAMPLES__, substr($strValue, 0, $intPosition), substr($strValue, $intPosition + 1));
					}
				}
				
				_p('</ul>', false);
			}
?>
		</blockquote></div></div>
		
		<br/>
		<div style="text-align: center;">
			For more information, please go to the Qcodo website at <b><a href="http://www.qcodo.com/" class="bodyLink">http://www.qcodo.com/</a></b>
			<br/><br/>
			If you have questions, comments or issues, you can discuss them in the forums at the <b><a href="http://www.qcodo.com/forums/forum.php/7" class="bodyLink">Examples Site Forum</a></b>
		</div>
		<br /><br /><br />



	</div>
</div>

<?php
	if (QApplication::PathInfo(0))
		printf('<script type="text/javascript">DisplayPart("%s");</script>', QApplication::PathInfo(0));
	require('includes/footer.inc.php');
?>