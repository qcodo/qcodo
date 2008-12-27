<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Integrating Optimistic Locking into QForms</div>
		
		In Section 2, we showed how by using the TIMESTAMP column types, Qcodo will generate
		code to handle <b>Optimistic Locking</b>.  In this example, we take this a step further
		to illustrate a more functional approach to utilizing <b>Optimistic Locking</b> in your
		web based application.<br/><br/>

		In our example below, we have the same <b>Person</b> object instantiated twice.  This
		is supposed to mimic two users on two different computers trying to edit the same
		<b>Person</b> object at the same time.<br/><br/>

		(Note: on some database platforms, including MySQL, no SQL UPDATE will be performed
		unless the data has actually been changed.  It's recommended that you make a change
		to either the <b>First Name</b> or the <b>Last Name</b> before hitting <b>Save</b>
		in order to see this example in action.)<br/><br/>

		As you can see, the <b>Optimstic Locking</b> functionality will allow both "users" to
		view the data.  But once one user tries to update one of the <b>Person</b> objects,
		the other <b>Person</b> object is recognized as "stale" (because of a TIMESTAMP
		mismatch).  Any subsequent call to <b>Save</b> on the "stale" <b>Person</b> will throw
		an exception.  We catch this <b>QOptimsiticLockingException</b> in our <b>QForm</b>
		in order to present a more graceful response to the user, allowing the user the option to
		override the changes made by the previous <b>Save</b> call, forcing the update.
	</div>

	<table cellspacing="10" cellpadding="10" border="0">
		<tr>
			<td align="center" colspan="2">
				Current <b>Name</b> and <b>Timestamp</b> values in the database for this <b>PersonWithLock</b> object:<br/>
				<b><?php _p($this->objPersonReference->FirstName . ' ' . $this->objPersonReference->LastName); ?></b>
				 &nbsp;|&nbsp; 
				<b><?php _p($this->objPersonReference->SysTimestamp); ?></b>
				<br/><?php $this->lblMessage->Render('ForeColor=Red','FontBold=true'); ?>
			</td>
		</tr>
		<tr>
			<td valign="top" style="width:300px;background-color:#ccffaa">
				<h3>PersonWithLock Instance #1</h3>
				<?php $this->txtFirstName1->RenderWithName('Name=First Name'); ?><br/>
				<?php $this->txtLastName1->RenderWithName('Name=Last Name'); ?><br/>
				<?php $this->lblTimestamp1->RenderWithName('Name=Timestamp Value'); ?><br/>
				<?php $this->btnSave1->Render('Text=Save This Person Object'); ?><br/><br/>
				<?php $this->btnForceUpdate1->Render('Text=Save This Person Object (Force Update)'); ?><br/>
			</td>
			<td valign="top" style="width:300px;background-color:#ccffaa">
				<h3>PersonWithLock Instance #2</h3>
				<?php $this->txtFirstName2->RenderWithName('Name=First Name'); ?><br/>
				<?php $this->txtLastName2->RenderWithName('Name=Last Name'); ?><br/>
				<?php $this->lblTimestamp2->RenderWithName('Name=Timestamp Value'); ?><br/>
				<?php $this->btnSave2->Render('Text=Save This Person Object'); ?><br/><br/>
				<?php $this->btnForceUpdate2->Render('Text=Save This Person Object (Force Update)'); ?><br/>
			</td>
		</tr>
	</table>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>