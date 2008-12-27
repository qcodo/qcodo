<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Implementing Cryptography</div>

		The <b>QCryptography</b> class is used to implement cryptography for your site and
		back-end.  It primarly serves as a cohesive wrapper around the <b>libmcrypt</b> library,
		which must also be installed.  (According to the PHP documentation, you must have
		<b>libmcrypt</b> v2.5.6 or greater for PHP 5)<br/><br/>

		By default, <b>QCryptography</b> will use the <b>TripleDES</b> cipher in <b>Electronic
		Codebook</b> mode.  It will also conveniently do base64 conversion (similar to MIME-based
		Base64 encoding) so that the resulting encrypted data can be used in text-based streams,
		GET/POST data, URLs, etc.<br/><br/>
		
		However, note that any of these options can be changed at any time.  Through the <b>libmcrypt</b>
		library, <b>QCryptography</b> supports most of the industry accepted ciphers,
		including <b>DES</b>, <b>ARC4</b>, <b>Blowfish</b>, <b>Rijndael</b>, <b>RC2</b>, <b>RC4</b>,
		<b>RC6</b>, etc.<br/><br/>

		You can statically specify a "default" cipher, mode, base64 flag and key by modifying
		the appropriate static member variable on the class, or you can specify these fields
		explicitly when constructing a new instance of <b>QCryptography</b>.<br/><br/>
		
		<b>QCryptography</b> also supports the encryption and decryption of entire files.<br/><br/>

		For more information about the <b>libmcrypt</b> library, please refer to the
		<a href="http://www.php.net/manual/en/ref.mcrypt.php" class="bodyLink">PHP Documentation</a>.<br/><br/>
	</div>

	<h3>TripleDES, Electronic Codebook Encryption</h3>
<?php
	$strOriginal = 'The quick brown fox jumps over the lazy dog.';

	// Modify the cipher and base64 mode by modifying the "default" cipher and mode on the class, itself

	// Specify a Key (this would typically be defined as a constant (e.g. in _configuration.inc)
	QCryptography::$Key = 'SampleKey';

	// By default, let's leave Base64 encoding turned off
	QCryptography::$Base64 = false;

	$objCrypto = new QCryptography();
	$strEncrypted = $objCrypto->Encrypt($strOriginal);
	$strDecrypted = $objCrypto->Decrypt($strEncrypted);

	printf('Original Data: <b>%s</b><br/>', $strOriginal);
	printf('Encrypted Data: <b>%s</b><br/>', $strEncrypted);
	printf('Decrypted Data: <b>%s</b><br/><br/><br/>', $strDecrypted);
?>



	<h3>TripleDES, Electronic Codebook Encryption (with Base64 encoding)</h3>
<?php
	$strOriginal = 'Just keep examining every low bid quoted for zinc etchings.';

	// Modify the base64 mode while making the specification on the constructor, itself

	// By default, let's instantiate a QCryptography object with Base64 encoding enabled
	// Note: while the resulting encrypted data is safe for any text-based stream, including
	// use as GET/POST data, inside the URL, etc., the resulting encrypted data stream will
	// be 33% larger.
	$objCrypto = new QCryptography(null, true);
	$strEncrypted = $objCrypto->Encrypt($strOriginal);
	$strDecrypted = $objCrypto->Decrypt($strEncrypted);

	printf('Original Data: <b>%s</b><br/>', $strOriginal);
	printf('Encrypted Data: <b>%s</b><br/>', $strEncrypted);
	printf('Decrypted Data: <b>%s</b><br/><br/><br/>', $strDecrypted);
	
?>

<?php require('../includes/footer.inc.php'); ?>