<?php
	/*
	 * The following line should require() the prepend.inc.php file
	 * in your includes directory.  This can either be a relative
	 * or an absolute path, but it is recommended to use a relative
	 * path, especially for systems that use multiple instances of Qcodo.
	 * Feel free to modify as needed.
	 */
	require(dirname(__FILE__) . '/../includes/prepend.inc.php');
	
    require_once('PHPUnit/Framework.php');
	
    class ValidateEmailTest extends PHPUnit_Framework_TestCase {

        /* Valid Addresses */

        public function testValidAddress_Standard() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('test@example.com'));
        }
		
        public function testValidAddress_UpperCaseLocalPart() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('TEST@example.com'));
        }

        public function testValidAddress_NumericLocalPart() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('1234567890@example.com'));
        }

        public function testValidAddress_TaggedLocalPart() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('test+test@example.com'));
        }

        public function testValidAddress_QmailLocalPart() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('test-test@example.com'));
        }

        public function testValidAddress_UnusualCharactersInLocalPart() {
        	$this->assertEquals(true, QEmailUtils::ValidateEmail('~@example.com'));
            $this->assertEquals(true, QEmailUtils::ValidateEmail('t*est@example.com'));
            $this->assertEquals(true, QEmailUtils::ValidateEmail('+1~1+@example.com'));
            $this->assertEquals(true, QEmailUtils::ValidateEmail('{_test_}@example.com'));
        }

        public function testValidAddress_QuotedLocalPart() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('"[[ test ]]"@example.com'));
        }

        public function testValidAddress_AtomisedLocalPart() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('test.test@example.com'));
        }

        public function testValidAddress_ObsoleteLocalPart() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('test."test"@example.com'));
        }

        public function testValidAddress_QuotedAtLocalPart() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('"test@test"@example.com'));
        }

        public function testValidAddress_IpDomain() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('test@123.123.123.123'));
        }

        public function testValidAddress_BracketIpDomain() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('test@[123.123.123.123]'));
        }

        public function testValidAddress_MultipleLabelDomain() {
            $this->assertEquals(true, QEmailUtils::ValidateEmail('test@example.example.com'));
            $this->assertEquals(true, QEmailUtils::ValidateEmail('test@example.example.example.com'));
        }

        /* Invalid Addresses */

        public function testInvalidAddress_TooLong() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345@example.com'));
        }

        public function testInvalidAddress_TooShort() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('@a'));
        }

        public function testInvalidAddress_NoAtSymbol() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test.example.com'));
        }

        public function testInvalidAddress_BlankAtomInLocalPart() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test.@example.com'));
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test..test@example.com'));
            $this->assertEquals(false, QEmailUtils::ValidateEmail('.test@example.com'));
        }

        public function testInvalidAddress_MultipleAtSymbols() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@test@example.com'));
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@@example.com'));
        }
	
	    public function testInvalidAddress_QuotedPairs() {
	        $this->assertEquals(false, QEmailUtils::ValidateEmail('test\"test@example.com'));
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test\@test@example.com'));
		}

        public function testInvalidAddress_InvalidCharactersInLocalPart() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('-- test --@example.com')); // No spaces allowed in local part
            $this->assertEquals(false, QEmailUtils::ValidateEmail('[test]@example.com')); // Square brackets only allowed within quotes
            $this->assertEquals(false, QEmailUtils::ValidateEmail('"test"test"@example.com')); // Quotes cannot be nested
            $this->assertEquals(false, QEmailUtils::ValidateEmail('()[]\;:,<>@example.com')); // Disallowed Characters
        }

        public function testInvalidAddress_DomainLabelTooShort() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@.'));
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@example.'));
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@.org'));
        }

        public function testInvalidAddress_LocalPartTooLong() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('12345678901234567890123456789012345678901234567890123456789012345@example.com')); // 64 characters is maximum length for local part. This is 65.
        }

        public function testInvalidAddress_DomainLabelTooLong() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012.com')); // 255 characters is maximum length for domain. This is 256.
        }

        public function testInvalidAddress_TooFewLabelsInDomain() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@example'));
        }
		
        public function testInvalidAddress_DoubleCommasInDomain() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@example..com'));
        }

        public function testInvalidAddress_UnpartneredSquareBracketIp() {
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@[123.123.123.123'));
            $this->assertEquals(false, QEmailUtils::ValidateEmail('test@123.123.123.123]'));
        }
    }
?>