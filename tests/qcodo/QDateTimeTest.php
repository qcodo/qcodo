<?php
	class QDateTimeTest extends PHPUnit_Framework_TestCase {
		
		protected $dttOne;
		protected $dttTwo;
		protected $arrTimes;
		protected $arrDates;
		protected $arrDatetimes;
		
		protected function setUp() { 
			$this->dttOne = new QDateTime();
			$this->dttTwo = new QDateTime();
			$this->arrTimes = array( QDateTime::FromTimeOnly('00:00:00'), QDateTime::FromTimeOnly('06:35:00'), QDateTime::FromTimeOnly('23:59:59'));
			$this->arrDates = array( new QDateTime('1980-06-13'), new QDateTime('today'));
			$this->arrDatetimes = array( new QDateTime('1980-06-13 04:34'), new QDateTime(QDateTime::Now) );
		} 
		
		protected function tearDown() {
			$this->dttOne = null;
			$this->dttTwo = null;
		}
		
		public function testEmpty() {
			$this->assertTrue($this->dttOne->IsDateNull());
			$this->assertTrue($this->dttOne->IsTimeNull());
			$this->assertTrue($this->dttTwo->IsDateNull());
			$this->assertTrue($this->dttTwo->IsTimeNull());
			
			$this->assertTrue($this->dttOne->IsEqualTo($this->dttTwo));
			$this->assertFalse($this->dttOne->IsEarlierThan($this->dttTwo));
			$this->assertTrue($this->dttOne->IsEarlierOrEqualTo($this->dttTwo));
			$this->assertFalse($this->dttOne->IsLaterThan($this->dttTwo));
			$this->assertTrue($this->dttOne->IsLaterOrEqualTo($this->dttTwo));
		}
		
		public function testCompareDatetimeAndTime() {
			$this->dttOne->setTime(0,0,3);
	   		$this->dttTwo->setDate(2000, 1, 1);
			$this->dttTwo->setTime(0,0,2);
			
			$this->assertTrue($this->dttOne->IsDateNull());
			$this->assertFalse($this->dttOne->IsTimeNull());
			$this->assertFalse($this->dttTwo->IsDateNull());
			$this->assertFalse($this->dttTwo->IsTimeNull());
			
			$this->assertFalse($this->dttOne->IsEqualTo($this->dttTwo));
			$this->assertFalse($this->dttOne->IsEarlierThan($this->dttTwo));
			$this->assertFalse($this->dttOne->IsEarlierOrEqualTo($this->dttTwo));
			$this->assertFalse($this->dttOne->IsLaterThan($this->dttTwo));
			$this->assertFalse($this->dttOne->IsLaterOrEqualTo($this->dttTwo));
		}
		
		public function testCompareDatetimeWithAddedMontAndTime() {
			$this->dttOne->setTime(0,0,3);
	   		$this->dttTwo->setDate(2000, 1, 1);
			$this->dttTwo->setTime(0,0,2);
			$this->dttTwo->Month++;
			
			$this->assertTrue($this->dttOne->IsDateNull());
			$this->assertFalse($this->dttOne->IsTimeNull());
			$this->assertFalse($this->dttTwo->IsDateNull());
			$this->assertFalse($this->dttTwo->IsTimeNull());
			
			$this->assertFalse($this->dttOne->IsEqualTo($this->dttTwo));
			$this->assertFalse($this->dttOne->IsEarlierThan($this->dttTwo));
			$this->assertFalse($this->dttOne->IsEarlierOrEqualTo($this->dttTwo));
			$this->assertFalse($this->dttOne->IsLaterThan($this->dttTwo));
			$this->assertFalse($this->dttOne->IsLaterOrEqualTo($this->dttTwo));
		}
		
		public function testTimes() {
			foreach ($this->arrTimes as $dtt) {
				$this->assertTrue($dtt->IsDateNull());
				$this->assertFalse($dtt->IsTimeNull());
			}
		}
		
		public function testDates() {
			foreach ($this->arrDates as $dtt) {
				$this->assertFalse($dtt->IsDateNull());
				$this->assertTrue($dtt->IsTimeNull());
			}
		}
		
		public function testDatetimes() {
			foreach ($this->arrDatetimes as $dtt) {
				$this->assertFalse($dtt->IsDateNull());
				$this->assertFalse($dtt->IsTimeNull());
			}
		}
	}
?>