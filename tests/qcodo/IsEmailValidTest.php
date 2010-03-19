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
	
    class IsEmailValidTest extends PHPUnit_Framework_TestCase {
    	
        /* Valid Addresses */

        public function testIsEmailValidTest_Standard() {
            $this->assertTrue(QEmailUtils::IsEmailValid('test@example.com'));
        }
		
        public function testIsEmailValidTest_UpperCaseLocalPart() {
            $this->assertTrue(QEmailUtils::IsEmailValid('TEST@example.com'));
        }

        public function testIsEmailValidTest_NumericLocalPart() {
            $this->assertTrue(QEmailUtils::IsEmailValid('1234567890@example.com'));
        }

        public function testIsEmailValidTest_TaggedLocalPart() {
            $this->assertTrue(QEmailUtils::IsEmailValid('test+test@example.com'));
        }

        public function testIsEmailValidTest_QmailLocalPart() {
            $this->assertTrue(QEmailUtils::IsEmailValid('test-test@example.com'));
        }

        public function testIsEmailValidTest_UnusualCharactersInLocalPart() {
        	$this->assertTrue(QEmailUtils::IsEmailValid('~@example.com'));
            $this->assertTrue(QEmailUtils::IsEmailValid('t*est@example.com'));
            $this->assertTrue(QEmailUtils::IsEmailValid('+1~1+@example.com'));
            $this->assertTrue(QEmailUtils::IsEmailValid('{_test_}@example.com'));
        }

        public function testIsEmailValidTest_QuotedLocalPart() {
            $this->assertTrue(QEmailUtils::IsEmailValid('"[[ test ]]"@example.com'));
        }

        public function testIsEmailValidTest_AtomisedLocalPart() {
            $this->assertTrue(QEmailUtils::IsEmailValid('test.test@example.com'));
        }

        public function testIsEmailValidTest_ObsoleteLocalPart() {
            $this->assertTrue(QEmailUtils::IsEmailValid('test."test"@example.com'));
        }

        public function testIsEmailValidTest_QuotedAtLocalPart() {
            $this->assertTrue(QEmailUtils::IsEmailValid('"test@test"@example.com'));
        }

        public function testIsEmailValidTest_IpDomain() {
            $this->assertTrue(QEmailUtils::IsEmailValid('test@123.123.123.123'));
        }

        public function testIsEmailValidTest_BracketIpDomain() {
            $this->assertTrue(QEmailUtils::IsEmailValid('test@[123.123.123.123]'));
        }

        public function testIsEmailValidTest_MultipleLabelDomain() {
            $this->assertTrue(QEmailUtils::IsEmailValid('test@example.example.com'));
            $this->assertTrue(QEmailUtils::IsEmailValid('test@example.example.example.com'));
        }

        /* Invalid Addresses */

        public function testInIsEmailValidTest_TooLong() {
            $this->assertFalse(QEmailUtils::IsEmailValid('12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345@example.com'));
        }

        public function testInIsEmailValidTest_TooShort() {
            $this->assertFalse(QEmailUtils::IsEmailValid('@a'));
        }

        public function testInIsEmailValidTest_NoAtSymbol() {
            $this->assertFalse(QEmailUtils::IsEmailValid('test.example.com'));
        }

        public function testInIsEmailValidTest_BlankAtomInLocalPart() {
            $this->assertFalse(QEmailUtils::IsEmailValid('test.@example.com'));
            $this->assertFalse(QEmailUtils::IsEmailValid('test..test@example.com'));
            $this->assertFalse(QEmailUtils::IsEmailValid('.test@example.com'));
        }

        public function testInIsEmailValidTest_MultipleAtSymbols() {
            $this->assertFalse(QEmailUtils::IsEmailValid('test@test@example.com'));
            $this->assertFalse(QEmailUtils::IsEmailValid('test@@example.com'));
        }
	
	    public function testInIsEmailValidTest_QuotedPairs() {
	        $this->assertFalse(QEmailUtils::IsEmailValid('test\"test@example.com'));
            $this->assertFalse(QEmailUtils::IsEmailValid('test\@test@example.com'));
		}

        public function testInIsEmailValidTest_InvalidCharactersInLocalPart() {
            $this->assertFalse(QEmailUtils::IsEmailValid('-- test --@example.com')); // No spaces allowed in local part
            $this->assertFalse(QEmailUtils::IsEmailValid('[test]@example.com')); // Square brackets only allowed within quotes
            $this->assertFalse(QEmailUtils::IsEmailValid('"test"test"@example.com')); // Quotes cannot be nested
            $this->assertFalse(QEmailUtils::IsEmailValid('()[]\;:,<>@example.com')); // Disallowed Characters
        }

        public function testInIsEmailValidTest_DomainLabelTooShort() {
            $this->assertFalse(QEmailUtils::IsEmailValid('test@.'));
            $this->assertFalse(QEmailUtils::IsEmailValid('test@example.'));
            $this->assertFalse(QEmailUtils::IsEmailValid('test@.org'));
        }

        public function testInIsEmailValidTest_LocalPartTooLong() {
            $this->assertFalse(QEmailUtils::IsEmailValid('12345678901234567890123456789012345678901234567890123456789012345@example.com')); // 64 characters is maximum length for local part. This is 65.
        }

        public function testInIsEmailValidTest_DomainLabelTooLong() {
            $this->assertFalse(QEmailUtils::IsEmailValid('test@123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012.com')); // 255 characters is maximum length for domain. This is 256.
        }

        public function testInIsEmailValidTest_TooFewLabelsInDomain() {
            $this->assertFalse(QEmailUtils::IsEmailValid('test@example'));
        }
		
        public function testInIsEmailValidTest_DoubleCommasInDomain() {
            $this->assertFalse(QEmailUtils::IsEmailValid('test@example..com'));
        }

        public function testInIsEmailValidTest_UnpartneredSquareBracketIp() {
            $this->assertFalse(QEmailUtils::IsEmailValid('test@[123.123.123.123'));
            $this->assertFalse(QEmailUtils::IsEmailValid('test@123.123.123.123]'));
        }
    }
?>