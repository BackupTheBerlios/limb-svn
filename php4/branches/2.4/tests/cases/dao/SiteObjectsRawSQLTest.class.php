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
require_once(dirname(__FILE__) . '/SiteObjectsSQLBaseTest.class.php');

class SiteObjectsRawSQLTest extends SiteObjectsSQLBaseTest
{
  function SiteObjectsRawSQLTest()
  {
    parent :: SiteObjectsSQLBaseTest('site objects raw sql tests');
  }

  function setUp()
  {
    parent :: setUp();
    $this->sql = new SiteObjectsRawSQL();
  }

  function testFindRequiredFields()
  {
    $this->sql->addCondition('sso.id = 1');

    $stmt =& $this->conn->newStatement($this->sql->toString());
    $rs =& new SimpleDbDataset($stmt->getRecordSet());
    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_1');
    $this->assertEqual($record['title'], 'object_1_title');
    $this->assertTrue(array_key_exists('modified_date', $record));
    $this->assertTrue(array_key_exists('created_date', $record));
    $this->assertTrue(array_key_exists('creator_id', $record));
    $this->assertEqual($record['locale_id'], 'en');
    $this->assertTrue(array_key_exists('site_object_id', $record));
    $this->assertTrue(array_key_exists('node_id', $record));
    $this->assertEqual($record['parent_node_id'], $this->root_node_id);
    $this->assertTrue(array_key_exists('level', $record));
    $this->assertTrue(array_key_exists('priority', $record));
    $this->assertTrue(array_key_exists('children', $record));
    $this->assertEqual($record['class_id'], $this->class_id);
    $this->assertEqual($record['class_name'], 'site_object');
    $this->assertTrue(array_key_exists('behaviour_id', $record));
    $this->assertEqual($record['behaviour'], 'site_object_behaviour');
    $this->assertTrue(array_key_exists('icon', $record));
    $this->assertTrue(array_key_exists('sort_order', $record));
    $this->assertTrue(array_key_exists('can_be_parent', $record));
  }

  function testOrderBy()
  {
    $this->sql->addOrder('sso.id', 'DESC');

    $stmt =& $this->conn->newStatement($this->sql->toString());
    $rs =& new SimpleDbDataset($stmt->getRecordSet());
    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_10');
    $this->assertEqual($record['title'], 'object_10_title');
  }

  function testFindNoParams()
  {
    $stmt =& $this->conn->newStatement($this->sql->toString());
    $rs =& $stmt->getRecordSet();

    for($i = 0,$rs->rewind();$rs->valid();$rs->next())
    {
      $i++;
      $record = $rs->current();
      $this->assertEqual($record->get('identifier'), 'object_' . $i);
      $this->assertEqual($record->get('title'), 'object_' . $i . '_title');
    }
  }
}
?>
