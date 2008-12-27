<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Emailing via SMTP</div>

		The <b>QEmailServer</b> class can be used to send email messages via any accessible
		SMTP server.  Obviously, many PHP developers will be familiar with the PHP <b>mail()</b>
		function, which internally makes a shell call to <b>sendmail</b>.  But note that for
		<i>many</i> reasons (including security, maintenance and deployability), <b>QEmailServer</b>
		does <i>not</i> use PHP's <b>mail()</b> nor does it use <b>sendmail</b>.<br/><br/>

		<b>QEmailServer</b> is an abstract class which only has static variables (which
		define the location of the SMTP server and other preferences) and a single static
		<b>Send</b> method.  The <b>Send</b> method takes in a <b>QEmailMessage</b> object.<br/><br/>

		The <b>QEmailMessage</b> object contains the relavent email addresses (e.g. From,
		To, Cc and Bcc), as well as the subject and body.  Note that the body can be in
		either plain text, HTML or both.  Qcodo will automatically handle the multipart
		message encoding for you.<br/><br/>
		
		Finally, note that for development environments that do not have ready access
		to an SMTP server, the <b>QEmailServer</b> can be set to <b>TestMode</b>,
		where communication between the application and the SMTP server will be written
		to disk instead of an SMTP socket.  This allows developers to develop and test
		email capability without actually sending out any emails.<br/><br/>

		Feel free to View Source the code.  Note that the final <b>Send</b> call is
		commented out, so this page is actually non-functional.  But you can view the
		code to get a sense as to how the <b>QEmailServer</b> and its associated
		<b>QEmailMessage</b> class work.
	</div>
	
	For obvious reasons, this page is non-functional.  To view the commented out source,
	please click on <b>View Source</b> at the top right of the page.

<?php
	// We want to define our email SMTP server (it defaults to "localhost")
	// This would typically be done in prepend.inc, and its value should probably be a constant
	// that is defined in _configuration.inc
	QEmailServer::$SmtpServer = 'mx.acme.com';

	// Create a new message
	// Note that you can list multiple addresses and that Qcodo supports Bcc and Cc
	$objMessage = new QEmailMessage();
	$objMessage->From = 'ACME Reporting Service <reporting@acme.com>';
	$objMessage->To = 'John Doe <jdoe@acme.com>, Jane Doe <jdoe2@acme.com>';
	$objMessage->Bcc = 'audit-system@acme.com';
	$objMessage->Subject = 'Report for ' . QDateTime::NowToString(QDateTime::FormatDisplayDate);

	// Setup Plaintext Message
	$strBody = "Dear John and Jane Doe,\r\n\r\n";
	$strBody .= "You have new reports to review.  Please go to the ACME Portal at http://portal.acme.com/ to review.\r\n\r\n";
	$strBody .= "Regards,\r\nACME Reporting Service";
	$objMessage->Body = $strBody;

	// Also setup HTML message (optional)
	$strBody = 'Dear John and Jane Doe,<br/><br/>';
	$strBody .= '<b>You have new reports to review.</b>  Please go to the <a href="http://portal.acme.com/">ACME Portal</a> to review.<br/><br/>';
	$strBody .= 'Regards,<br/><b>ACME Reporting Service</b>';
	$objMessage->HtmlBody = $strBody;

	// Add random/custom email headers
	$objMessage->SetHeader('x-application', 'ACME Reporting Service v1.2a');

	// Send the Message (Commented out for obvious reasons)
//	QEmailServer::Send($objMessage);



	// Note that you can also shortcut the Send command to one line for simple messages (similar to PHP's mail())
	$strBody = "Dear John and Jane Doe,\r\n\r\n";
	$strBody .= "You have new reports to review.  Please go to the ACME Portal at http://portal.acme.com/ to review.\r\n\r\n";
	$strBody .= "Regards,\r\nACME Reporting Service";
//	QEmailServer::Send(new QEmailMessage('reporting@acme.com', 'jdoe@acme.com', 'Alerts Received!', $strBody));
?>

<?php require('../includes/footer.inc.php'); ?>