<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Using a QControlProxy to Receive Events</div>
		Sometimes you may want to create buttons, links or other HTML items which can "trigger" a Server or Ajax
		action without actually creating a control.  The typical example of this is if you want to dynamically
		create a large number of links or buttons (e.g. in a <b>QDataGrid</b> or <b>QDataRepeater</b>) which would trigger
		an action, but because the link/button doesn't have any other state (e.g. you'll never want to
		change its value or style, or you're comfortable doing this in pure javascript), you don't want to
		incur the overhead of creating a whole <b>QControl</b> for each of these links or buttons.
		<br/><br/>
		
		The way you can do this is by creating a <b>QControlProxy</b> on your <b>QForm</b>, and having
		any manually created links or buttons make hard-coded <b>RenderAsEvents()</b> method calls to
		trigger your action/event.<br/><br/>

		The example below illustrates the manual creation (see the code for more information) of a list of
		links which makes use of a single <b>QControlProxy</b> to trigger our event.  Notice that while there are 4 links
		and 4 buttons which each trigger Ajax-based Actions, there is actually only 1 <b>QControl</b> (which of course is
		the <b>QControlProxy</b> control itself) defined to handle all these events.
	</div>

		<h3>These A HREF links can take advantage of <i>all</i> Events defined on our proxy control by using RenderAsEvents...</h3>
		<a href="#" <?php $this->pxyExample->RenderAsEvents('Baz'); ?>">Baz</a> | 
		<a href="#" <?php $this->pxyExample->RenderAsEvents('Foo'); ?>">Foo</a> | 
		<a href="#" <?php $this->pxyExample->RenderAsEvents('Blah'); ?>">Blah</a> | 
		<a href="#" <?php $this->pxyExample->RenderAsEvents('Test'); ?>">Test</a>
		<br/><br/>

		<h3>Same goes for any other HTML element, like buttons...</h3>
		<input type="button" <?php $this->pxyExample->RenderAsEvents('Test 1') ?> value="Test #1"> | 
		<input type="button" <?php $this->pxyExample->RenderAsEvents('Test 2') ?> value="Test #2"> | 
		<input type="button" <?php $this->pxyExample->RenderAsEvents('Test 3') ?> value="Test #3"> | 
		<input type="button" <?php $this->pxyExample->RenderAsEvents('Test 4') ?> value="Test #4">

		<br/><br/>

		<?php $this->lblMessage->Render(); ?>
		<?php $this->pnlHover->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>