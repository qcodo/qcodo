<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">SOAP Web Services</div>

		The Qcodo <b>QSoapService</b> class allows you to very easily add SOAP-enabled
		PHP methods to your application.<br/><br/>
		
		It utilizes the <b>SoapServer</b> class in PHP 5.1 or greater
		(and subsequently, this specific example also uses the <b>SoapClient</b> class
		to illustrate the <b>QSoapService</b> in action).
		
		However, <b>QSoapService</b> adds a significant level of functionality,
		overcoming most of the inherent issues found in the PHP <b>SoapServer</b>, including:
		<ul>
		<li>Full, Automatic WSDL Generation</li>
		<li>Supports parameters that are Pass By Reference</li>
		<li>Supports <b>QDateTime</b>, arrays and other input/output parameters that
		are complex data structures</li>
		<li>And of course, full support for code generated data objects</li>
		</ul>
		
		To use, simply create a class which extends from <b>QSoapService</b>, and define any
		public methods as you would any other class (static, private and protected
		methods can be defined in this class as well, but they will not be exposed as 
		publically available webservice methods).
		<br/><br/>
		
		Please note is that because SOAP by definition is a strongly-typed
		protocol, and because PHP is not, you must declare the types of the input and output
		parameters for each webservice-enabled method.  This is done through standard
		PHPDoc tags.  Please view the code for <b>example_service.php</b> for more information.
		<br/><br/>
		
		And finally, due to the heavy processing of WSDL generation, the results of the WSDL generation
		are cached using <b>QCache</b>, and cached files are stored in <b>/includes/cache/soap</b>.
	</div>

<?php
	// PHP's WSDL Caching Mechanism doesn't work well -- be sure to turn it off
	// if you are using SoapClient
	ini_set('soap.wsdl_cache_enabled', false);

	// Use Built-in PHP 5.1 functionality to make webservice SOAP calls to example_service.php
	// obviously, we could do this using Microsoft.NET, J2EE, etc., as well
	$strUrl = sprintf('http://%s%s/communication/example_service.php',
		$_SERVER['HTTP_HOST'],
		__VIRTUAL_DIRECTORY__ . __EXAMPLES__);

	$strWsdlUrl = sprintf('%s?wsdl', $strUrl);
	$objClient = new SoapClient($strWsdlUrl);
?>
	<div>
		To <b>view</b> the webservice, please go to <a href="<?php _p($strUrl); ?>"><?php _p($strUrl); ?></a>
		<br/>
		To <b>use</b> the webservice, use any SOAP compatible client and import the WSDL file at
		<a href="<?php _p($strWsdlUrl); ?>"><?php _p($strWsdlUrl); ?></a>
		<br/><br/>
	
		Example call on <b>ExampleService</b>:<br/><br/>
	
		<b>AddNumbers(15, 22):</b>
		<blockquote><div><?php _p($objClient->AddNumbers(15, 22)); ?></div></blockquote>
		
		<b>GetDate(12, 25, 2007):</b>
		<blockquote><div>
			<?php _p($objClient->GetDate(12, 25, 2007)); ?>
			<br/>(notice how the resulting QDateTime has been converted to a SOAP-compliant dateTime)
		</div></blockquote>

		<b>GetPeople('Smith'):</b>
		<ul>
<?php
		$objPeople = $objClient->GetPeople('Smith');
		foreach ($objPeople as $objPerson)
			printf('<li>%s %s</li>', $objPerson->FirstName, $objPerson->LastName);
?>
		</ul>
	</div>

<?php require('../includes/footer.inc.php'); ?>