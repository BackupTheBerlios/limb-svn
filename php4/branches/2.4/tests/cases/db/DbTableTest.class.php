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
require_once(LIMB_DIR . '/class/db_tables/DbTableFactory.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');

class Test1DbTable extends DbTable
{
  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'description' => '',
      'title' => '',
      'date_field' => array('type' => 'date'),
      'int_field' => array('type' => 'numeric'),
      'int' => array('type' => 'numeric'),
    );
  }

  function _defineConstraints()
  {
    return array(
      'object_id' =>	array(
          0 => array(
            'table_name' => 'test2',
            'field' => 'image_id',
          ),
      ),
    );
  }
}


class DbTableTest extends LimbTestCase
{
  var $db = null;
  var $db_table_test = null;

  function setUp()
  {
    $this->db =& DbFactory :: instance();
    $this->db_table_test = DbTableFactory :: create('Test1');

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('test1');
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
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'wow!'));

    $this->assertNotEqual($this->db->sqlExec("SELECT * FROM test1"), array());

    $result = $this->db->fetchRow();
    $this->assertTrue(is_array($result));
    $id = $this->db_table_test->getLastInsertId();

    $this->assertEqual($result['title'], 'wow');
    $this->assertEqual($result['description'], 'wow!');
    $this->assertEqual($result['id'], $id);
  }

  function testUpdate()
  {
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description2'));

    $this->db_table_test->update(array('description' =>  'new_description'), array('title' =>  'wow'));

    $this->assertEqual($this->db->getAffectedRows(), 2);

    $this->assertNotEqual($this->db->sqlExec("SELECT * FROM test1"), array());

    $result = $this->db->getArray();
    $this->assertEqual($result[0]['description'], 'new_description');
    $this->assertEqual($result[1]['description'], 'new_description');
  }

  function testUpdateByInCondition()
  {
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow1', 'description' => 'description2'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'yo', 'description' => 'description3'));

    $this->db_table_test->update(
      array('description' =>  'new_description'),
      sqlIn('title', array('wow', 'wow1'))
    );

    $this->assertEqual($this->db->getAffectedRows(), 2);

    $this->assertNotEqual($this->db->sqlExec("SELECT * FROM test1"), array());

    $result = $this->db->getArray();
    $this->assertEqual($result[0]['description'], 'new_description');
    $this->assertEqual($result[1]['description'], 'new_description');
  }

  function testUpdateById()
  {
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description2'));

    $this->assertNotEqual($this->db->sqlExec("SELECT * FROM test1"), array());

    $result = $this->db->getArray();

    $this->db_table_test->updateById($result[0]['id'], array('description' =>  'new_description'));

    $this->assertEqual($this->db->getAffectedRows(), 1);

    $this->assertNotEqual($this->db->sqlExec("SELECT * FROM test1"), array());
    $result = $this->db->getArray();
    $this->assertEqual($result[0]['description'], 'new_description');
  }

  function testGetListAll()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->getList();

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 2);

    $arr = reset($result);
    $this->assertEqual($arr['description'], 'description');

    $arr = next($result);
    $this->assertEqual($arr['description'], 'description2');
  }

  function testGetListByStringCondition()
  {
    $this->assertEqual($this->db_table_test->getList('title="wow"'), array());

    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->getList(
      "title='wow!' AND description='description2'");

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 1);

    $arr = reset($result);
    $this->assertEqual($arr['description'], 'description2');
  }

  function testGetListByArrayCondition()
  {
    $this->assertEqual($this->db_table_test->getList(array('title' => 'wow!')), array());

    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->getList(
      array('title' => 'wow!', 'description' => 'description2'));

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 1);

    $arr = reset($result);
    $this->assertEqual($arr['description'], 'description2');
  }

  function testGetListByInCondition()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->getList(
      sqlIn('title', array('wow!', 'wow')));

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 2);

    $arr = reset($result);
    $this->assertEqual($arr['description'], 'description');

    $arr = next($result);
    $this->assertEqual($arr['description'], 'description2');
  }

  function testGetListWithLimit()
  {
    $data = array(
      0 => array('id' => null, 'title' =>  'wow', 'description' => 'description'),
      1 => array('id' => null, 'title' =>  'wow!', 'description' => 'description2'),
      2 => array('id' => null, 'title' =>  'wow2', 'description' => 'description3'),
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);
    $this->db_table_test->insert($data[2]);

    $result = $this->db_table_test->getList('', 'id', '', 1, 1);

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 1);

    $arr = reset($result);
    $this->assertEqual($arr['title'], 'wow!');
    $this->assertEqual($arr['description'], 'description2');
  }

  function testGetColumnsForSelectDefaultName()
  {
    $select_string = 'test1.id as id, test1.description as description, test1.title as title, test1.date_field as date_field, test1.int_field as int_field, test1.int as int';

    $this->assertEqual($this->db_table_test->getColumnsForSelect(), $select_string);
  }

  function testGetColumnsForSelectSpecificName()
  {
    $select_string = 'tn.id as id, tn.description as description, tn.title as title, tn.date_field as date_field, tn.int_field as int_field, tn.int as int';

    $this->assertEqual($this->db_table_test->getColumnsForSelect('tn'), $select_string);
  }

  function testGetColumnsForSelectSpecificNameWithExcludes()
  {
    $select_string = 'tn.title as title, tn.date_field as date_field, tn.int_field as int_field, tn.int as int';

    $this->assertEqual($this->db_table_test->getColumnsForSelect('tn', array('id', 'description')), $select_string);
  }

}
?>
