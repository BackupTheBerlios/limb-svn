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
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');

class SimpleDbTest extends LimbTestCase
{
  var $db = null;
  var $conn = null;

  function SimpleDbTest()
  {
    parent :: LimbTestCase('simple db tests');
  }

  function setUp()
  {
    $this->conn =& LimbDbPool :: getConnection();
    $this->db = new SimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $stmt = $this->conn->newStatement('DELETE FROM test1');
    $stmt->execute();
  }

  function testInsert()
  {
    $id = $this->db->insert('test1', array('id' => null,
                                           'title' =>  'wow',
                                           'description' => 'wow!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test1");
    $record = $stmt->getOneRecord();

    $this->assertEqual($record->get('title'), 'wow');
    $this->assertEqual($record->get('description'), 'wow!');
    $this->assertEqual($record->get('id'), $id);
  }

  function testUpdateAll()
  {
    $this->db->insert('test1', array('id' => null, 'title' =>  'wow', 'description' => 'description' ));
    $this->db->insert('test1', array('id' => null, 'title' =>  'wow', 'description' => 'description2'));

    $this->assertEqual($this->db->update('test1', array('description' =>  'new_description')), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test1");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');

    $records->next();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
  }

  function testUpdateByCondition()
  {
    $this->db->insert('test1', array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db->insert('test1', array('id' => null, 'title' =>  'wow', 'description' => 'description2'));
    $this->db->insert('test1', array('id' => null, 'title' =>  'yo', 'description' => 'description3'));

    $res = $this->db->update('test1',
                              array('description' =>  'new_description', 'title' => 'wow2'),
                              array('title' => 'wow'));

    $this->assertEqual($res, 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test1");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
    $this->assertEqual($record->get('title'), 'wow2');

    $records->next();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
    $this->assertEqual($record->get('title'), 'wow2');
  }

  function testUpdateByMixedCondition()
  {
    $this->db->insert('test1', array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db->insert('test1', array('id' => null, 'title' =>  'wow', 'description' => 'description2'));
    $this->db->insert('test1', array('id' => null, 'title' =>  'yo', 'description' => 'description3'));

    $res = $this->db->update('test1',
                              array('description' =>  'new_description', 'title' => 'wow2'),
                              array('title' => 'wow', "description='description2'"));

    $this->assertEqual($res, 1);

    $stmt = $this->conn->newStatement("SELECT * FROM test1");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'description');
    $this->assertEqual($record->get('title'), 'wow');

    $records->next();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
    $this->assertEqual($record->get('title'), 'wow2');

    $records->next();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'description3');
    $this->assertEqual($record->get('title'), 'yo');
  }

  function testSelectAll()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow', 'description' => 'description2')
    );

    $this->db->insert('test1', $data[0]);
    $this->db->insert('test1', $data[1]);

    $result = $this->db->select('test1');

    $this->assertEqual($result->getTotalRowCount(), 2);

    $result->rewind();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description');

    $result->next();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testSelectAllUsingAsterisk()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow', 'description' => 'description2')
    );

    $this->db->insert('test1', $data[0]);
    $this->db->insert('test1', $data[1]);

    $result = $this->db->select('test1', '*');

    $this->assertEqual($result->getTotalRowCount(), 2);

    $result->rewind();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description');

    $result->next();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testSelectByCondition()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
    );

    $this->db->insert('test1', $data[0]);
    $this->db->insert('test1', $data[1]);

    $result = $this->db->select('test1',
                                array('*'),
                                array('title' => 'wow!',
                                      'description' => 'description2'));

    $this->assertEqual($result->getTotalRowCount(), 1);

    $result->rewind();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testSelectByMixedCondition()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2'),
    );

    $this->db->insert('test1', $data[0]);
    $this->db->insert('test1', $data[1]);

    $result = $this->db->select('test1',
                                array('*'),
                                array("title = 'wow!'",
                                      'description' => 'description2'));

    $this->assertEqual($result->getTotalRowCount(), 1);

    $result->rewind();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testDeleteAll()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
    );

    $this->db->insert('test1', $data[0]);
    $this->db->insert('test1', $data[1]);

    $this->assertEqual($this->db->delete('test1'), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test1");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->getTotalRowCount(), 0);
  }

  function testDeleteByMixedCondition()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
    );

    $this->db->insert('test1', $data[0]);
    $this->db->insert('test1', $data[1]);

    $this->assertEqual($this->db->delete('test1',
                                         array('description' => 'description',
                                               "description='no-such-descr'")), 0);

    $stmt = $this->conn->newStatement("SELECT * FROM test1");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->getTotalRowCount(), 2);
  }
}
?>
