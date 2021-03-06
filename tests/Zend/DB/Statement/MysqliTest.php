<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @namespace
 */
namespace ZendTest\DB\Statement;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Statement
 */
class MySQLiTest extends AbstractTest
{

    public function setup()
    {
        $this->markTestSkipped('This suite is skipped until Zend\DB can be refactored.');
    }
    
    public function testStatementRowCount()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->prepare("DELETE FROM $products WHERE $product_id = 1");

        $n = $stmt->rowCount();
        $this->assertType('integer', $n);
        $this->assertEquals(-1, $n, 'Expecting row count to be -1 before executing query');

        $stmt->execute();

        $n = $stmt->rowCount();
        $stmt->closeCursor();

        $this->assertType('integer', $n);
        $this->assertEquals(1, $n, 'Expected row count to be one after executing query');
    }

    public function testStatementBindParamByName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $productIdValue   = 4;
        $productNameValue = 'AmigaOS';

        try {
            $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (:id, :name)");
            // test with colon prefix
            $this->assertTrue($stmt->bindParam(':id', $productIdValue), 'Expected bindParam(\':id\') to return true');
            // test with no colon prefix
            $this->assertTrue($stmt->bindParam('name', $productNameValue), 'Expected bindParam(\'name\') to return true');
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->assertEquals("Invalid bind-variable name ':id'", $e->getMessage());
        }
    }

    public function testStatementBindValueByName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $productIdValue   = 4;
        $productNameValue = 'AmigaOS';

        try {
            $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (:id, :name)");
            // test with colon prefix
            $this->assertTrue($stmt->bindParam(':id', $productIdValue), 'Expected bindParam(\':id\') to return true');
            // test with no colon prefix
            $this->assertTrue($stmt->bindParam('name', $productNameValue), 'Expected bindParam(\'name\') to return true');
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->assertEquals("Invalid bind-variable name ':id'", $e->getMessage());
        }
    }

    public function testStatementGetColumnMeta()
    {
        $this->markTestIncomplete($this->getDriver() . ' has not implemented getColumnMeta() yet [ZF-1424]');
    }

    /**
     * Tests ZF-3216, that the statement object throws exceptions that
     * contain the numerica MySQL SQLSTATE error code
     * @group ZF-3216
     */
    public function testStatementExceptionShouldContainErrorCode()
    {
        $sql = "SELECT * FROM *";
        try {
            $stmt = $this->_db->query($sql);
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('int', $e->getCode());
        }
    }

    /**
     * @group ZF-7706
     */
    public function testStatementCanReturnDriverStatement()
    {
        $statement = parent::testStatementCanReturnDriverStatement();
        $this->assertType('mysqli_stmt', $statement->getDriverStatement());
    }

    /**
     * Tests that the statement returns FALSE when no records are found
     * @group ZF-5675
     */
    public function testStatementReturnsFalseOnEmpty()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $sql = 'SELECT * FROM ' . $products . ' WHERE 1=2';
        $stmt = $this->_db->query($sql);
        $result = $stmt->fetch();
        $this->assertFalse($result);
    }


    public function getDriver()
    {
        return 'Mysqli';
    }

}
