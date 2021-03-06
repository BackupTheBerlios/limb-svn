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
require_once(LIMB_DIR . '/core/db_tables/LimbDbTableFactory.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class Test1DbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'test_db_table';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'description' => '',
      'title' => '',
    );
  }
}


class LimbDbTableTest extends LimbTestCase
{
  var $conn = null;
  var $db_table_test = null;

  function LimbDbTableTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db_table_test = LimbDbTableFactory :: create('Test1');

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $stmt = $this->conn->newStatement('DELETE FROM test_db_table');
    $stmt->execute();
  }

  function testCorrectTableProperties()
  {
    $this->assertEqual($this->db_table_test->getTableName(), 'test_db_table');
    $this->assertEqual($this->db_table_test->getPrimaryKeyName(), 'id');
    $this->assertEqual($this->db_table_test->getColumnType('id'), 'numeric');
    $this->assertIdentical($this->db_table_test->getColumnType('no_column'), false);
    $this->assertTrue($this->db_table_test->hasColumn('id'));
    $this->assertTrue($this->db_table_test->hasColumn('description'));
    $this->assertTrue($this->db_table_test->hasColumn('title'));
    $this->assertFalse($this->db_table_test->hasColumn('no_such_a_field'));
  }

  function testInsert()
  {
    $id = $this->db_table_test->insert(array('title' =>  'wow',
                                             'description' => 'wow!',
                                             'junk!!!' => 'junk!!!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEqual($record->get('title'), 'wow');
    $this->assertEqual($record->get('description'), 'wow!');
    $this->assertEqual($record->get('id'), $id);
  }

  function testUpdateAll()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description' ));
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description2'));

    $this->db_table_test->update(array('description' =>  'new_description',
                                       'junk!!!' => 'junk!!!'));

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
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
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description2'));
    $this->db_table_test->insert(array('title' =>  'yo', 'description' => 'description3'));

    $this->db_table_test->update(
      array('description' =>  'new_description',
            'title' => 'wow2',
            'junk!!!' => 'junk!!!'),
      array('title' => 'wow',
            'junk!!!' => 'junk!!!')
    );

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
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
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description2'));
    $this->db_table_test->insert(array('title' =>  'yo', 'description' => 'description3'));

    $res = $this->db_table_test->update(array('description' =>  'new_description', 'title' => 'wow2'),
                                        array('title' => 'wow', "description='description2'"));

    $this->assertEqual($res, 1);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
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

  function testUpdateById()
  {
    $id = $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow2', 'description' => 'description2'));

    $this->db_table_test->updateById($id, array('description' =>  'new_description'));

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 1);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();
    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
  }

  function testSelectAll()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->select();

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
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->select(
      array('title' => 'wow!',
            'description' => 'description2',
            'junk!!!' => 'junk!!!'));

    $this->assertEqual($result->getTotalRowCount(), 1);

    $result->rewind();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testSelectByMixedCondition()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2'),
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->select(array("title = 'wow!'",
                                                 'description' => 'description2'));

    $this->assertEqual($result->getTotalRowCount(), 1);

    $result->rewind();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testSelectRecordById()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $id = $this->db_table_test->insert($data[1]);

    $record = $this->db_table_test->selectRecordById($id);
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testSelectRecordByIdNotFound()
  {
    $this->assertNull($this->db_table_test->selectRecordById(1));
  }

  function testDeleteAll()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->delete();

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->getTotalRowCount(), 0);
  }

  function testDeleteByMixedCondition()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->delete(array('description' => 'description',
                                       "description='no-such-descr'"));

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 0);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->getTotalRowCount(), 2);
  }

  function testDeleteById()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $id = $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->deleteById($id);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->getTotalRowCount(), 1);

    $records->rewind();

    $record =& $records->current();
    $this->assertEqual($record->get('title'), 'wow!');
  }

  function testGetColumnsForSelectDefaultName()
  {
    $select_string = 'test_db_table.id as id, test_db_table.description as description, test_db_table.title as title';

    $this->assertEqual($this->db_table_test->getColumnsForSelectAsString(), $select_string);
  }

  function testGetColumnsForSelectSpecificNameAndPrefix()
  {
    $select_string = 'tn.id as _content_id, tn.description as _content_description, tn.title as _content_title';

    $this->assertEqual($this->db_table_test->getColumnsForSelectAsString('tn', array(), '_content_'), $select_string);
  }

  function testGetColumnsForSelectSpecificNameWithExcludes()
  {
    $select_string = 'tn.title as title';

    $this->assertEqual($this->db_table_test->getColumnsForSelectAsString('tn', array('id', 'description')), $select_string);
  }
}
?>
