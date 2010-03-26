<?php
    class QTypeTest extends PHPUnit_Framework_TestCase {		

        public function testPositiveIntegerToBoolean(){
            $blnResult = QType::Cast(1, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testNegativeIntegerToBoolean(){
            $blnResult = QType::Cast(-1, QType::Boolean);            
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testZeroIntegerToBoolean(){
            $blnResult = QType::Cast(0, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertFalse($blnResult);
        }

        public function testLongIntegerToBoolean(){
            $blnResult = QType::Cast(15236985269854752361258631231434342341123, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testPositiveFloatToBoolean(){
            $blnResult = QType::Cast(1.50, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testNegativeFloatToBoolean(){
            $blnResult = QType::Cast(-1.50, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testZeroFloatToBoolean(){
            $blnResult = QType::Cast(0.00, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertFalse($blnResult);
        }

        public function testLongFloatToBoolean(){
            $blnResult = QType::Cast(123123123123123123123.1231231235454334324, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testNegativeLongFloatToBoolean(){
            $blnResult = QType::Cast(-123123123123123123123.1231231235454334324, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testMixedStringToBoolean(){            
            $blnResult = QType::Cast('1425qcodo', QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testEmptyStringToBoolean(){
            $blnResult = QType::Cast('', QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertFalse($blnResult);
        }

        public function testStringToBoolean(){
            $blnResult = QType::Cast('qcodo', QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testStringZeroToBoolean(){
            $blnResult = QType::Cast('0', QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertFalse($blnResult);
        }

        public function testStringOneToBoolean(){
            $blnResult = QType::Cast('1', QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testStringNegativeToBoolean(){
            $blnResult = QType::Cast('-1', QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testLongStringToBoolean(){
            $blnResult = QType::Cast('123123123123123123123.1231231235454334324', QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testStringNullToBoolean(){
            $blnResult = QType::Cast('null', QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testNullToBoolean(){
            $blnResult = QType::Cast(null, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NULL, $blnResult);
            $this->assertNull($blnResult);
        }

        public function testBooleanTrueToBoolean(){
            $blnResult = QType::Cast(true, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertTrue($blnResult);
        }

        public function testBooleanFalseToBoolean(){
            $blnResult = QType::Cast(false, QType::Boolean);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_BOOL, $blnResult);
            $this->assertFalse($blnResult);
        }

        public function testNullToFloat(){
            $fltResult = QType::Cast(null, QType::Float);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NULL, $fltResult);
            $this->assertNull($fltResult);
        }

        public function testStringToFloat(){
            $this->setExpectedException('QInvalidCastException');
            $fltResult = QType::Cast('qcodo', QType::Float);
        }

        public function testMixedStringToFloat(){
            $this->setExpectedException('QInvalidCastException');
            $fltResult = QType::Cast('1425qcodo', QType::Float);
        }

        public function testLongStringToFloat(){
            $fltResult = QType::Cast('123123123123123123123.1231231235454334324', QType::Float);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_FLOAT, $fltResult);
            $this->assertEquals(123123123123123123123.1231231235454334324, $fltResult);
        }
        
        public function testNegativeLongStringToFloat(){
            $fltResult = QType::Cast('-123123123123123123123.1231231235454334324', QType::Float);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_FLOAT, $fltResult);
            $this->assertEquals(-123123123123123123123.1231231235454334324, $fltResult);
        }

        public function testBooleanFalseToFloat(){            
            $fltResult = QType::Cast(false, QType::Float);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_FLOAT, $fltResult);
        }

        public function testBooleanTrueToFloat(){            
            $fltResult = QType::Cast(true, QType::Float);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_FLOAT, $fltResult);
        }

        public function testNullToInteger(){
            $intResult = QType::Cast(null, QType::Integer);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NULL, $intResult);
            $this->assertNull($intResult);
        }

        public function testStringToInteger(){
            $this->setExpectedException('QInvalidCastException');
            $intResult = QType::Cast('qcodo', QType::Integer);
        }

        public function testMixedStringToInteger(){
            $this->setExpectedException('QInvalidCastException');
            $intResult = QType::Cast('1425qcodo', QType::Integer);
        }

        public function testLongStringToInteger(){
            $intResult = QType::Cast('123123123123123123123.1231231235454334324', QType::Integer);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_INT, $intResult);
            $this->assertEquals(123123123123123123123, $intResult);
        }

        public function testNegativeLongStringToInteger(){
            $fltResult = QType::Cast('-123123123123123123123.1231231235454334324', QType::Integer);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_INT, $intResult);
            $this->assertEquals(-123123123123123123123, $intResult);
        }

        public function testBooleanFalseToInteger(){
            $intResult = QType::Cast(false, QType::Integer);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_INT, $intResult);
            $this->assertEquals(0,$intResult);
        }

        public function testBooleanTrueToInteger(){
            $intResult = QType::Cast(true, QType::Integer);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_INT, $intResult);
            $this->assertEquals(1,$intResult);
        }
    }
?>
