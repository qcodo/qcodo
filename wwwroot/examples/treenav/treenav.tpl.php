<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Tree Navigation Control</div>

		This example shows off the <b>QTreeNav</b> control.<br/><br/>

		The control uses it's own internal tree data structure, combined with javascript/DOM caching and
		recursion to store and render the items/nodes within the tree navigation.<br/><br/>
		
		Note that the <i>first</i> time you expand a node, the tree navigation item will make a <b>postajax</b>
		call to retrieve the child nodes for that node.  However, on subsequent expand/collapse events
		for that node, it's purely client-side (no <b>postajax</b> call is made).<br/><br/>
		
		Finally, please be sure to view the <b>tnvExample_AddItems</b> call in the <b>treenav.php</b>
		code to see how we recurse through the includes/ filesystem directory to recursively add the treenav
		nodes/items to the tree nav control.
	</div>

	<?php $this->tnvExample->Render(); ?>
	<?php $this->pnlCode->Render(); ?>

	<p><?php $this->objDefaultWaitIcon->Render('Position=Absolute','Top=430px','Left=40px'); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>