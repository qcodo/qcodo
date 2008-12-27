<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>
	
	<div class="instructions">
		<div class="instruction_title">More "J" and Less "A" in AJAX</div>

		Because our Renameable Labels make full use of <b>QAjaxActions</b>, any clicking (including
		just selecting a label) involves an asynchronous server hit.<br/><br/>

		Of course, by having all your functionality and display logic in one place, we show
		how you can quickly and rapidly develop AJAX interactions with very little PHP code,
		and in fact with <i>no</i> custom JavaScript whatsoever.  This allows developers
		the ability to rapidly prototype not just web-based
		applications, but also web-based applications with full AJAX functionality.<br/><br/>

		But as your application matures, you may want to have some fully server-side AJAX functionality
		be converted into more performance-efficient client-side-only JavaScript functionality.
		This example shows how you can easily change an existing <b>QForm</b> that uses all Qcodo-based AJAX
		interactions into a more blended server- and client-side javascript/AJAX form.  Because the API for
		<b>QServerActions</b>, <b>QJavaScriptActions</b> and <b>QAjaxActions</b> are all the same, the
		process for rewriting specific nuggets of functionality in this manner is straightforward,
		and the action types (from Ajax- to JavaScript- to Server-) should be very interchangable.
		<br/><br/>

	</div>

		<script type="text/javascript">
			var intSelectedIndex = -1;
			var objSelectedLabel;

			function lblArray_Click(objControl) {
				var strControlId = objControl.id;
				var intIndex = strControlId.substr(5);
				
				// Is the Label being clicked already selected?
				if (intSelectedIndex == intIndex) {
					// It's already selected -- go ahead and replace it with the textbox
					qcodo.getWrapper(strControlId).toggleDisplay('hide');
					qcodo.getWrapper('textbox' + intIndex).toggleDisplay('show');

					var objTextbox = qcodo.getControl('textbox' + intIndex);
					objTextbox.value = objControl.innerHTML;
					objTextbox.focus();
					objTextbox.select();
				} else {
					// Nope -- not yet selected
	
					// First, unselect everything else
					if (objSelectedLabel)
						objSelectedLabel.className = 'renamer_item';
	
					// Now, make this item selected
					objControl.className = 'renamer_item renamer_item_selected';
					objSelectedLabel = objControl;
					intSelectedIndex = intIndex;					
				}
			}
		</script>

		<?php for ($intIndex = 0; $intIndex < 10; $intIndex++) {
			_p('<p style="height: 16px;">', false);
			$this->lblArray[$intIndex]->Render();
			$this->txtArray[$intIndex]->Render('BorderWidth=1px','BorderColor=gray','BorderStyle=Solid');
			_p('</p>', false);
		} ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>