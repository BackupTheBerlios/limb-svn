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
require_once(LIMB_DIR . '/class/db_tables/LimbDbTableFactory.class.php');
require_once(LIMB_DIR . '/class/lib/db/LimbDbTable.class.php');

class Test1DbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'test1';
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
  var $db = null;
  var $db_table_test = null;

  function LimbDbTableTest()
  {
    parent :: LimbTestCase('db table tests');
  }

  function setUp()
  {
    $this->db =& LimbDbPool :: getConnection();
    $this->db_table_test = LimbDbTableFactory :: create('Test1');

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $stmt = $this->db->newStatement('DELETE FROM test1');
    $stmt->execute();
  }

  function testCorrectTableProperties()
  {
    $this->assertEqual($this->db_table_test->getTableName(), 'test1');
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
    $id = $this->db_table_test->insert(array('id' => null,
                                             'title' =>  'wow',
                                             'description' => 'wow!',
                                             'junk!!!' => 'junk!!!'));

    $stmt = $this->db->newStatement("SELECT * FROM test1");
    $record = $stmt->getOneRecord();

    $this->assertEqual($record->get('title'), 'wow');
    $this->assertEqual($record->get('description'), 'wow!');
    $this->assertEqual($record->get('id'), $id);
  }

  function testUpdateAll()
  {
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description' ));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description2'));

    $this->db_table_test->update(array('description' =>  'new_description',
                                       'junk!!!' => 'junk!!!'));

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 2);

    $stmt = $this->db->newStatement("SELECT * FROM test1");
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
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description2'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'yo', 'description' => 'description3'));

    $this->db_table_test->update(
      array('description' =>  'new_description',
            'title' => 'wow2',
            'junk!!!' => 'junk!!!'),
      array('title' => 'wow',
            'junk!!!' => 'junk!!!')
    );

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 2);

    $stmt = $this->db->newStatement("SELECT * FROM test1");
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

  function testUpdateById()
  {
    $id = $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow2', 'description' => 'description2'));

    $this->db_table_test->updateById($id, array('description' =>  'new_description'));

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 1);

    $stmt = $this->db->newStatement("SELECT * FROM test1");
    $records = $stmt->getRecordSet();
    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
  }

  function testSelectAll()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow', 'description' => 'description2')
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
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
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

  function testSelectRecordById()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $id = $this->db_table_test->insert($data[1]);

    $record = $this->db_table_test->selectRecordById($id);
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testDeleteAll()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->delete();

    $stmt = $this->db->newStatement("SELECT * FROM test1");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->getTotalRowCount(), 0);
  }

  function testDeleteById()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
    );

    $id = $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->deleteById($id);

    $stmt = $this->db->newStatement("SELECT * FROM test1");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->getTotalRowCount(), 1);

    $records->rewind();

    $record =& $records->current();
    $this->assertEqual($record->get('title'), 'wow!');
  }

  function testGetColumnsForSelectDefaultName()
  {
    $select_string = 'test1.id as id, test1.description as description, test1.title as title';

    $this->assertEqual($this->db_table_test->getColumnsForSelectAsString(), $select_string);
  }

  function testGetColumnsForSelectSpecificName()
  {
    $select_string = 'tn.id as id, tn.description as description, tn.title as title';

    $this->assertEqual($this->db_table_test->getColumnsForSelectAsString('tn'), $select_string);
  }

  function testGetColumnsForSelectSpecificNameWithExcludes()
  {
    $select_string = 'tn.title as title';

    $this->assertEqual($this->db_table_test->getColumnsForSelectAsString('tn', array('id', 'description')), $select_string);
  }
}
?>
