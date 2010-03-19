<?php
	/*
	 * The following line should require() the prepend.inc.php file
	 * in your includes directory.  This can either be a relative
	 * or an absolute path, but it is recommended to use a relative
	 * path, especially for systems that use multiple instances of Qcodo.
	 * Feel free to modify as needed.
	 */
	require(dirname(__FILE__) . '/../../includes/prepend.inc.php');
	
    require_once('PHPUnit/Framework.php');
	
    class ValidateEmailTest extends PHPUnit_Framework_TestCase {
    	
        /* Valid Addresses */

        public function testValidAddress_Standard() {
            $this->assertTrue(QEmailUtils::ValidateEmail('test@example.com'));
        }
		
        public function testValidAddress_UpperCaseLocalPart() {
            $this->assertTrue(QEmailUtils::ValidateEmail('TEST@example.com'));
        }

        public function testValidAddress_NumericLocalPart() {
            $this->assertTrue(QEmailUtils::ValidateEmail('1234567890@example.com'));
        }

        public function testValidAddress_TaggedLocalPart() {
            $this->assertTrue(QEmailUtils::ValidateEmail('test+test@example.com'));
        }

        public function testValidAddress_QmailLocalPart() {
            $this->assertTrue(QEmailUtils::ValidateEmail('test-test@example.com'));
        }

        public function testValidAddress_UnusualCharactersInLocalPart() {
        	$this->assertTrue(QEmailUtils::ValidateEmail('~@example.com'));
            $this->assertTrue(QEmailUtils::ValidateEmail('t*est@example.com'));
            $this->assertTrue(QEmailUtils::ValidateEmail('+1~1+@example.com'));
            $this->assertTrue(QEmailUtils::ValidateEmail('{_test_}@example.com'));
        }

        public function testValidAddress_QuotedLocalPart() {
            $this->assertTrue(QEmailUtils::ValidateEmail('"[[ test ]]"@example.com'));
        }

        public function testValidAddress_AtomisedLocalPart() {
            $this->assertTrue(QEmailUtils::ValidateEmail('test.test@example.com'));
        }

        public function testValidAddress_ObsoleteLocalPart() {
            $this->assertTrue(QEmailUtils::ValidateEmail('test."test"@example.com'));
        }

        public function testValidAddress_QuotedAtLocalPart() {
            $this->assertTrue(QEmailUtils::ValidateEmail('"test@test"@example.com'));
        }

        public function testValidAddress_IpDomain() {
            $this->assertTrue(QEmailUtils::ValidateEmail('test@123.123.123.123'));
        }

        public function testValidAddress_BracketIpDomain() {
            $this->assertTrue(QEmailUtils::ValidateEmail('test@[123.123.123.123]'));
        }

        public function testValidAddress_MultipleLabelDomain() {
            $this->assertTrue(QEmailUtils::ValidateEmail('test@example.example.com'));
            $this->assertTrue(QEmailUtils::ValidateEmail('test@example.example.example.com'));
        }

        /* Invalid Addresses */

        public function testInvalidAddress_TooLong() {
            $this->assertFalse(QEmailUtils::ValidateEmail('12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345@example.com'));
        }

        public function testInvalidAddress_TooShort() {
            $this->assertFalse(QEmailUtils::ValidateEmail('@a'));
        }

        public function testInvalidAddress_NoAtSymbol() {
            $this->assertFalse(QEmailUtils::ValidateEmail('test.example.com'));
        }

        public function testInvalidAddress_BlankAtomInLocalPart() {
            $this->assertFalse(QEmailUtils::ValidateEmail('test.@example.com'));
            $this->assertFalse(QEmailUtils::ValidateEmail('test..test@example.com'));
            $this->assertFalse(QEmailUtils::ValidateEmail('.test@example.com'));
        }

        public function testInvalidAddress_MultipleAtSymbols() {
            $this->assertFalse(QEmailUtils::ValidateEmail('test@test@example.com'));
            $this->assertFalse(QEmailUtils::ValidateEmail('test@@example.com'));
        }
	
	    public function testInvalidAddress_QuotedPairs() {
	        $this->assertFalse(QEmailUtils::ValidateEmail('test\"test@example.com'));
            $this->assertFalse(QEmailUtils::ValidateEmail('test\@test@example.com'));
		}

        public function testInvalidAddress_InvalidCharactersInLocalPart() {
            $this->assertFalse(QEmailUtils::ValidateEmail('-- test --@example.com')); // No spaces allowed in local part
            $this->assertFalse(QEmailUtils::ValidateEmail('[test]@example.com')); // Square brackets only allowed within quotes
            $this->assertFalse(QEmailUtils::ValidateEmail('"test"test"@example.com')); // Quotes cannot be nested
            $this->assertFalse(QEmailUtils::ValidateEmail('()[]\;:,<>@example.com')); // Disallowed Characters
        }

        public function testInvalidAddress_DomainLabelTooShort() {
            $this->assertFalse(QEmailUtils::ValidateEmail('test@.'));
            $this->assertFalse(QEmailUtils::ValidateEmail('test@example.'));
            $this->assertFalse(QEmailUtils::ValidateEmail('test@.org'));
        }

        public function testInvalidAddress_LocalPartTooLong() {
            $this->assertFalse(QEmailUtils::ValidateEmail('12345678901234567890123456789012345678901234567890123456789012345@example.com')); // 64 characters is maximum length for local part. This is 65.
        }

        public function testInvalidAddress_DomainLabelTooLong() {
            $this->assertFalse(QEmailUtils::ValidateEmail('test@123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012.com')); // 255 characters is maximum length for domain. This is 256.
        }

        public function testInvalidAddress_TooFewLabelsInDomain() {
            $this->assertFalse(QEmailUtils::ValidateEmail('test@example'));
        }
		
        public function testInvalidAddress_DoubleCommasInDomain() {
            $this->assertFalse(QEmailUtils::ValidateEmail('test@example..com'));
        }

        public function testInvalidAddress_UnpartneredSquareBracketIp() {
            $this->assertFalse(QEmailUtils::ValidateEmail('test@[123.123.123.123'));
            $this->assertFalse(QEmailUtils::ValidateEmail('test@123.123.123.123]'));
        }
    }
?>