<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/db_tables/LimbDbTableFactory.class.php');
require_once(LIMB_DIR . '/core/SysParam.class.php');

class SysParamsTest extends LimbTestCase
{
  var $db = null;

  function sysParamsTest()
  {
    $this->db =& new SimpleDb(LimbDbPool :: getConnection());
    parent :: LimbTestCase();
  }

  function setUp()
  {
    DebugMock :: init($this);

    $this->db->delete('sys_param');
  }

  function tearDown()
  {
    DebugMock :: tally();

    $this->db->delete('sys_param');
  }

  function testSaveCharValue()
  {
    $sp =& SysParam :: instance();

    $result = $sp->saveParam('param_1', 'char', 123);

    $this->assertNotNull($result);

    $db_table =& LimbDbTableFactory :: create('SysParam');
    $rs = $db_table->select();
    $list = $rs->getArray();
    $this->assertEqual(count($list) , 1);

    $record = current($list);

    $this->assertEqual($record['type'], 'char');
    $this->assertEqual($record['char_value'], '123');
    $this->assertNull($record['int_value']);
    $this->assertNull($record['float_value']);
    $this->assertNull($record['blob_value']);
  }

  function testSaveIntValue()
  {
    $sp =& SysParam :: instance();

    $result = $sp->saveParam('param_1', 'int', 123);

    $this->assertNotNull($result);

    $db_table =& LimbDbTableFactory :: create('SysParam');
    $rs = $db_table->select();
    $list = $rs->getArray();
    $this->assertEqual(count($list) , 1);
    $record = current($list);

    $this->assertEqual($record['type'], 'int');
    $this->assertEqual($record['int_value'], 123);
    $this->assertNull($record['char_value']);
    $this->assertNull($record['float_value']);
    $this->assertNull($record['blob_value']);
  }

  function testSaveFloatValue()
  {
    $sp =& SysParam :: instance();

    $result = $sp->saveParam('param_1', 'float', 123.053);

    $this->assertNotNull($result);

    $db_table =& LimbDbTableFactory :: create('SysParam');
    $rs = $db_table->select();
    $list = $rs->getArray();
    $this->assertEqual(count($list) , 1);
    $record = current($list);

    $this->assertEqual($record['type'], 'float');
    $this->assertEqual($record['float_value'], 123.053);
    $this->assertNull($record['int_value']);
    $this->assertNull($record['char_value']);
    $this->assertNull($record['blob_value']);
  }

  function testSaveBlob()
  {
    $sp =& SysParam :: instance();

    $result = $sp->saveParam('param_1', 'blob', 123.053);

    $this->assertNotNull($result);

    $db_table =& LimbDbTableFactory :: create('SysParam');
    $rs = $db_table->select();
    $list = $rs->getArray();
    $this->assertEqual(count($list) , 1);
    $record = current($list);

    $this->assertEqual($record['type'], 'blob');
    $this->assertEqual($record['blob_value'], 123.053);
    $this->assertNull($record['int_value']);
    $this->assertNull($record['char_value']);
    $this->assertNull($record['float_value']);
  }

  function testSaveMultitypeValue()
  {
    $sp =& SysParam :: instance();

    $result = $sp->saveParam('param_1', 'float', 123.053);
    $this->assertNotNull($result);
    $result = $sp->saveParam('param_1', 'int', 123.053);
    $this->assertNotNull($result);

    $db_table =& LimbDbTableFactory :: create('SysParam');
    $rs = $db_table->select();
    $list = $rs->getArray();
    $this->assertEqual(count($list) , 1);
    $record = current($list);

    $this->assertEqual($record['type'], 'int');
    $this->assertEqual($record['int_value'], 123);
    $this->assertEqual($record['float_value'],0);
    $this->assertEqual($record['char_value'],'');
    $this->assertEqual($record['blob_value'],'');

    $result = $sp->saveParam('param_1', 'char', 123.053, false);

    $this->assertNotNull($result);

    $rs = $db_table->select();
    $list = $rs->getArray();
    $this->assertEqual(count($list) , 1);
    $record = current($list);

    $this->assertEqual($record['type'], 'char');
    $this->assertEqual($record['char_value'],'123.053');
    $this->assertEqual($record['float_value'],0);
    $this->assertEqual($record['int_value'], 123);
    $this->assertEqual($record['blob_value'],'');
  }

  function testSaveWrongTypeValue()
  {
    $sp =& SysParam :: instance();

    $sp->saveParam('param_1', 'sadnkfjhskjfd', 123.053);
    $this->assertTrue(catch('Exception', $e));

    $this->assertEqual($e->getMessage(), 'trying to save undefined type in sys_param');
    $this->assertEqual($e->getAdditionalParams(),
      array (
        'type' => 'sadnkfjhskjfd',
        'param' => 'param_1',
      )
    );
  }

  function testGetValue()
  {
    $sp =& SysParam :: instance();

    $number = 123.053;
    $sp->saveParam('param_1', 'float', $number);

    $this->assertEqual($sp->getParam('param_1'), $number);
    $this->assertNull($sp->getParam('param_1', 'char'));
  }

  function testGetWrongTypeValue()
  {
    $sp =& SysParam :: instance();

    $number = 123.053;
    $sp->saveParam('param_1', 'float', $number);

    $sp->getParam('param_1', 'blabla');
    $this->assertTrue(catch('Exception', $e));

    $this->assertEqual($e->getMessage(), 'trying to get undefined type in sys_param');
    $this->assertEqual($e->getAdditionalParams(),
      array (
        'type' => 'blabla',
        'param' => 'param_1',
      )
    );
  }
}
?>