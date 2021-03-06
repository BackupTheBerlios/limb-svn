<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/db/db_table_factory.class.php');
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');

class test1_db_table extends db_table
{
  function test1_db_table()
  {
    parent :: db_table();
  }

  function _define_columns()
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

  function _define_constraints()
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


class db_table_test extends LimbTestCase
{
  var $db = null;
  var $db_table_test = null;

  function setUp()
  {
    $this->db =& db_factory :: instance();
    $this->db_table_test =& db_table_factory :: instance('test1');

    $this->_clean_up();
  }

  function tearDown()
  {
    $this->_clean_up();
  }

  function _clean_up()
  {
    $this->db->sql_delete('test1');
  }

  function test_instantiate()
  {
    $this->assertReference($this->db_table_test, db_table_factory :: instance('test1'));
  }

  function test_correct_table_properties()
  {
    $this->assertEqual($this->db_table_test->get_table_name(), 'test1');
    $this->assertEqual($this->db_table_test->get_primary_key_name(), 'id');
    $this->assertEqual($this->db_table_test->get_column_type('id'), 'numeric');
    $this->assertIdentical($this->db_table_test->get_column_type('no_column'), false);
    $this->assertTrue($this->db_table_test->has_column('id'));
    $this->assertTrue($this->db_table_test->has_column('description'));
    $this->assertTrue($this->db_table_test->has_column('title'));
    $this->assertFalse($this->db_table_test->has_column('no_such_a_field'));
  }

  function test_insert()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'wow!'));

    $this->assertNotEqual($this->db->sql_exec("SELECT * FROM test1"), array());

    $result = $this->db->fetch_row();
    $this->assertTrue(is_array($result));

    $id = $this->db_table_test->get_last_insert_id();

    $this->assertEqual($result['title'], 'wow');
    $this->assertEqual($result['description'], 'wow!');
    $this->assertEqual($result['id'], $id);
  }

  function test_update()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description2'));

    $this->db_table_test->update(array('description' =>  'new_description'), array('title' =>  'wow'));

    $this->assertEqual($this->db->get_affected_rows(), 2);

    $this->assertNotEqual($this->db->sql_exec("SELECT * FROM test1"), array());

    $result = $this->db->get_array();
    $this->assertEqual($result[0]['description'], 'new_description');
    $this->assertEqual($result[1]['description'], 'new_description');
  }

  function test_update_by_in_condition()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('title' =>  'wow1', 'description' => 'description2'));
    $this->db_table_test->insert(array('title' =>  'yo', 'description' => 'description3'));

    $this->db_table_test->update(
      array('description' =>  'new_description'),
      sql_in('title', array('wow', 'wow1'))
    );

    $this->assertEqual($this->db->get_affected_rows(), 2);

    $this->assertNotEqual($this->db->sql_exec("SELECT * FROM test1"), array());

    $result = $this->db->get_array();
    $this->assertEqual($result[0]['description'], 'new_description');
    $this->assertEqual($result[1]['description'], 'new_description');
  }

  function test_update_by_id()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description2'));

    $this->assertNotEqual($this->db->sql_exec("SELECT * FROM test1"), array());

    $result = $this->db->get_array();

    $this->db_table_test->update_by_id($result[0]['id'], array('description' =>  'new_description'));

    $this->assertEqual($this->db->get_affected_rows(), 1);

    $this->assertNotEqual($this->db->sql_exec("SELECT * FROM test1"), array());
    $result = $this->db->get_array();
    $this->assertEqual($result[0]['description'], 'new_description');
  }

  function test_get_list_all()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->get_list();

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 2);

    $arr = reset($result);
    $this->assertEqual($arr['description'], 'description');

    $arr = next($result);
    $this->assertEqual($arr['description'], 'description2');
  }

  function test_get_list_by_string_condition()
  {
    $this->assertEqual($this->db_table_test->get_list('title="wow"'), array());

    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->get_list(
      "title='wow!' AND description='description2'");

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 1);

    $arr = reset($result);
    $this->assertEqual($arr['description'], 'description2');
  }

  function test_get_list_by_array_condition()
  {
    $this->assertEqual($this->db_table_test->get_list(array('title' => 'wow!')), array());

    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->get_list(
      array('title' => 'wow!', 'description' => 'description2'));

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 1);

    $arr = reset($result);
    $this->assertEqual($arr['description'], 'description2');
  }

  function test_get_list_by_in_condition()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->get_list(
      sql_in('title', array('wow!', 'wow')));

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 2);

    $arr = reset($result);
    $this->assertEqual($arr['description'], 'description');

    $arr = next($result);
    $this->assertEqual($arr['description'], 'description2');
  }

  function test_get_list_with_limit()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2'),
      2 => array('title' =>  'wow2', 'description' => 'description3'),
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);
    $this->db_table_test->insert($data[2]);

    $result = $this->db_table_test->get_list('', 'id', '', 1, 1);

    $this->assertNotEqual($result, array());

    $this->assertEqual(sizeof($result), 1);

    $arr = reset($result);
    $this->assertEqual($arr['title'], 'wow!');
    $this->assertEqual($arr['description'], 'description2');
  }
}
?>