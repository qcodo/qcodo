<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Customizing How FormState is Saved</div>
		
		By default, the <b>QForm</b> engine will store the state of the actual <b>QForm</b> objects as a rather
		long <b>Base64</b> encoded string.  While this is a very simple, straightforward and very maintenance-free
		approach, it does cause some complications, especially for more enterprise-level application
		architectures:
		<ul>
		<li><b>Performance</b>: for really complex forms, formstate could account for as much as 10KB ~ 15KB or more of
		extra data being sent over the pipe.  Especially for highly interactive AJAX-based applications, where you
		can have potentially multiple simultaneous operations, this can become a major performance bottleneck.</li>
		<li><b>Security</b>: with just simple <b>Base64</b> encoding, a hacker could alter their own formstate and modify
		private member variables in the form that you don't intend to have modified.</li>
		</ul>
		
		Qcodo resolves this by offering the ability to store/handle the formstate in various ways.  You can store
		the formstate data in PHP Sessions or you can store the formstate data directly on the
		filesystem.  For both methods, you end up only passing a small key back to the user.  Moreover, the formstate,
		itself, or the key can even be encrypted, using the
		<b><a href="../communication/crypto.php" class="bodyLink">QCryptography</a></b> class.<br/><br/>
		
		Finally, because the FormState handler is encapsulated in its own class, you can even define your own formstate
		handler, to store the formstate data on a shared server, in a database, or even in server memory.<br/><br/>
		
		In our example below, we use <b>QSessionFormStateHandler</b> to store the formstate data in PHP Session, and we
		will only store the session key (in this case, just a simple integer) on the page as a hidden form variable.
		For an added level of security, we will also encrypt the key.<br/><br/>
		
		If you use your browser's "View Source" functionality, you will see that the <b>Qform__FormState</b> hidden
		form variable is now a <b>lot</b> shorter (likely about 10 - 20 bytes).  Compare this to the
		<a href="../basic_qform/intro.php" class="bodyLink">first example</a> where the form state was easily over 1 KB.  This is because
		the bulk of the form state is being stored as a PHP Session Variable, which is located on the server, itself.
	</div>

	<?php // We will override the ForeColor, FontBold and the FontSize.  Note how we can optionally
		  // add quotes around our value. ?>
	<p><?php $this->lblMessage->Render(); ?></p>
	<p><?php $this->btnButton->Render(); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>