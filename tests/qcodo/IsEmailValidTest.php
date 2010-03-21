<?php
	class IsEmailValidTest extends PHPUnit_Framework_TestCase {
		
		/* Valid Addresses */

		public function testIsEmailValidTest_Standard() {
			$this->assertTrue(QEmailServer::IsEmailValid('test@example.com'));
		}
		
		public function testIsEmailValidTest_UpperCaseLocalPart() {
			$this->assertTrue(QEmailServer::IsEmailValid('TEST@example.com'));
		}

		public function testIsEmailValidTest_NumericLocalPart() {
			$this->assertTrue(QEmailServer::IsEmailValid('1234567890@example.com'));
		}

		public function testIsEmailValidTest_TaggedLocalPart() {
			$this->assertTrue(QEmailServer::IsEmailValid('test+test@example.com'));
		}

		public function testIsEmailValidTest_QmailLocalPart() {
			$this->assertTrue(QEmailServer::IsEmailValid('test-test@example.com'));
		}

		public function testIsEmailValidTest_UnusualCharactersInLocalPart() {
			$this->assertTrue(QEmailServer::IsEmailValid('~@example.com'));
			$this->assertTrue(QEmailServer::IsEmailValid('t*est@example.com'));
			$this->assertTrue(QEmailServer::IsEmailValid('+1~1+@example.com'));
			$this->assertTrue(QEmailServer::IsEmailValid('{_test_}@example.com'));
		}

		public function testIsEmailValidTest_QuotedLocalPart() {
			$this->assertTrue(QEmailServer::IsEmailValid('"[[ test ]]"@example.com'));
		}

		public function testIsEmailValidTest_AtomisedLocalPart() {
			$this->assertTrue(QEmailServer::IsEmailValid('test.test@example.com'));
		}

		public function testIsEmailValidTest_ObsoleteLocalPart() {
			$this->assertTrue(QEmailServer::IsEmailValid('test."test"@example.com'));
		}

		public function testIsEmailValidTest_QuotedAtLocalPart() {
			$this->assertTrue(QEmailServer::IsEmailValid('"test@test"@example.com'));
		}

		public function testIsEmailValidTest_IpDomain() {
			$this->assertTrue(QEmailServer::IsEmailValid('test@123.123.123.123'));
		}

		public function testIsEmailValidTest_BracketIpDomain() {
			$this->assertTrue(QEmailServer::IsEmailValid('test@[123.123.123.123]'));
		}

		public function testIsEmailValidTest_MultipleLabelDomain() {
			$this->assertTrue(QEmailServer::IsEmailValid('test@example.example.com'));
			$this->assertTrue(QEmailServer::IsEmailValid('test@example.example.example.com'));
		}

		/* Invalid Addresses */

		public function testInIsEmailValidTest_TooLong() {
			$this->assertFalse(QEmailServer::IsEmailValid('12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345@example.com'));
		}

		public function testInIsEmailValidTest_TooShort() {
			$this->assertFalse(QEmailServer::IsEmailValid('@a'));
		}

		public function testInIsEmailValidTest_NoAtSymbol() {
			$this->assertFalse(QEmailServer::IsEmailValid('test.example.com'));
		}

		public function testInIsEmailValidTest_BlankAtomInLocalPart() {
			$this->assertFalse(QEmailServer::IsEmailValid('test.@example.com'));
			$this->assertFalse(QEmailServer::IsEmailValid('test..test@example.com'));
			$this->assertFalse(QEmailServer::IsEmailValid('.test@example.com'));
		}

		public function testInIsEmailValidTest_MultipleAtSymbols() {
			$this->assertFalse(QEmailServer::IsEmailValid('test@test@example.com'));
			$this->assertFalse(QEmailServer::IsEmailValid('test@@example.com'));
		}
	
		public function testInIsEmailValidTest_QuotedPairs() {
			$this->assertFalse(QEmailServer::IsEmailValid('test\"test@example.com'));
			$this->assertFalse(QEmailServer::IsEmailValid('test\@test@example.com'));
		}

		public function testInIsEmailValidTest_InvalidCharactersInLocalPart() {
			$this->assertFalse(QEmailServer::IsEmailValid('-- test --@example.com')); // No spaces allowed in local part
			$this->assertFalse(QEmailServer::IsEmailValid('[test]@example.com')); // Square brackets only allowed within quotes
			$this->assertFalse(QEmailServer::IsEmailValid('"test"test"@example.com')); // Quotes cannot be nested
			$this->assertFalse(QEmailServer::IsEmailValid('()[]\;:,<>@example.com')); // Disallowed Characters
		}

		public function testInIsEmailValidTest_DomainLabelTooShort() {
			$this->assertFalse(QEmailServer::IsEmailValid('test@.'));
			$this->assertFalse(QEmailServer::IsEmailValid('test@example.'));
			$this->assertFalse(QEmailServer::IsEmailValid('test@.org'));
		}

		public function testInIsEmailValidTest_LocalPartTooLong() {
			$this->assertFalse(QEmailServer::IsEmailValid('12345678901234567890123456789012345678901234567890123456789012345@example.com')); // 64 characters is maximum length for local part. This is 65.
		}

		public function testInIsEmailValidTest_DomainLabelTooLong() {
			$this->assertFalse(QEmailServer::IsEmailValid('test@123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012.com')); // 255 characters is maximum length for domain. This is 256.
		}

		public function testInIsEmailValidTest_TooFewLabelsInDomain() {
			$this->assertFalse(QEmailServer::IsEmailValid('test@example'));
		}
		
		public function testInIsEmailValidTest_DoubleCommasInDomain() {
			$this->assertFalse(QEmailServer::IsEmailValid('test@example..com'));
		}

		public function testInIsEmailValidTest_UnpartneredSquareBracketIp() {
			$this->assertFalse(QEmailServer::IsEmailValid('test@[123.123.123.123'));
			$this->assertFalse(QEmailServer::IsEmailValid('test@123.123.123.123]'));
		}
	}
?>