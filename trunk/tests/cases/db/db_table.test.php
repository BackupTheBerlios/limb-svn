<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/lib/db/db_table_factory.class.php');
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');
require_once(LIMB_DIR . '/core/lib/db/result_set.class.php');

class sql_builder_stub
{
	var $insert_criteria;
	
	var $update_criteria_fields;
	var $update_criteria_where;
	
	var $select_criteria;
	
	var $delete_criteria;
	
	var $connection;
	
	var $mock_rs;
	
	function do_insert($criteria, $connection)
	{
		$this->insert_criteria = $criteria;
		$this->connection = $connection;
		
		return 1000;
	}
	
	function do_update($select_criteria, $update_criteria, $connection)
	{
		$this->update_criteria_where = $select_criteria;
		$this->update_criteria_fields = $update_criteria;
		$this->connection = $connection;
		
		return true;
	}
	
	function & do_select($criteria, $connection)
	{
		$this->select_criteria = $criteria;
		$this->connection = $connection;
		
		return $this->mock_rs;
	}
	
	function do_delete($criteria, $connection)
	{
		$this->delete_criteria = $criteria;
		$this->connection = $connection;
		
		return true;
	}
}

class test_version_db_table extends db_table
{ 
	function & _get_sql_builder()
	{
		return new sql_builder_stub();
	}
	   
  function _define_columns()
  {
  	return array(
  		'id' => array('type' => db_types::NUMERIC()),
      'description' => array('type' => db_types::VARCHAR()),
      'title' => array('type' => db_types::VARCHAR()),
    );
  }
  
  function _define_constraints()
  {
    return array(
    	'id' =>	array(
	    		0 => array(
						'table_name' => 'test_dependent',
						'field' => 'dependent',
					),
			),
    );   
  }
}

Mock::generate('result_set');
Mock::generate('db_table', 'test_dependent_db_table');

//class test_dependent_db_table
//{
//	var $delete_criteria;
//	var $columns_map = array();
//	
//	var $delete_calls = 0;
//	var $has_column_calls = 0;
//	
//	function delete($criteria)
//	{
//		$this->delete_criteria = $criteria;
//		$this->delete_calls++;
//	}	
//	
//	function has_column($column)
//	{
//		$this->columns_map[] = $column;
//		$this->has_column_calls++;
//		
//		if($column == 'dependent')
//			return true;
//		else
//			return false;
//	}
//}

class test_db_table extends UnitTestCase
{
	var $sql_builder = null;
	var $test_db_table = null;
		
	function setUp()
	{
		$this->test_db_table =& db_table_factory :: instance('test_version');
		$this->sql_builder =& $this->test_db_table->_sql_builder;
		$this->sql_builder->mock_rs =& new Mockresult_set($this);
	}
	
	function tearDown()
	{
		$this->sql_builder->mock_rs->tally();
	}
			
	function test_instantiate()
	{
		$this->assertReference($this->test_db_table, db_table_factory :: instance('test_version'));
	} 
	
	function test_correct_table_properties()
	{		
		$this->assertEqual($this->test_db_table->get_table_name(), 'test_version');
		$this->assertEqual($this->test_db_table->get_primary_key_name(), 'id');
		
		$this->assertEqual($this->test_db_table->get_column_type('id'), db_types::NUMERIC());
		$this->assertEqual($this->test_db_table->get_column_type('description'), db_types::VARCHAR());
		$this->assertEqual($this->test_db_table->get_column_type('title'), db_types::VARCHAR());
		$this->assertIdentical($this->test_db_table->get_column_type('no_column'), false);
		
		$this->assertTrue($this->test_db_table->has_column('id'));
		$this->assertTrue($this->test_db_table->has_column('description'));		
		$this->assertTrue($this->test_db_table->has_column('title'));
		$this->assertFalse($this->test_db_table->has_column('no_such_a_field'));
	}
	
	function test_insert()
	{
		$this->assertEqual(1000, $this->test_db_table->insert(array('title' =>  'wow', 'description' => 'wow!')));
		
		$this->assertIsA($this->sql_builder->insert_criteria, 'criteria');
		$this->assertIsA($this->sql_builder->connection, 'connection');

		$i = $this->sql_builder->insert_criteria->get_iterator();
		$this->assertEqual($i->count(), 2);
		
		$this->assertEqual($this->sql_builder->insert_criteria->get('test_version.title'), 'wow');
		$this->assertEqual($this->sql_builder->insert_criteria->get('test_version.description'), 'wow!');		
	}
	
	function test_update()
	{	
		$criteria = new criteria();
		$this->assertTrue($this->test_db_table->update(array('title' =>  'wow', 'description' => 'wow!'), $criteria));

		$this->assertIsA($this->sql_builder->update_criteria_fields, 'criteria');
		$this->assertIsA($this->sql_builder->update_criteria_where, 'criteria');
		$this->assertIsA($this->sql_builder->connection, 'connection');
		
		$i = $this->sql_builder->update_criteria_fields->get_iterator();
		$this->assertEqual($i->count(), 2);
		
		$this->assertEqual($this->sql_builder->update_criteria_fields->get('test_version.title'), 'wow');
		$this->assertEqual($this->sql_builder->update_criteria_fields->get('test_version.description'), 'wow!');
	}
			
	function test_select()
	{
		$criteria = new criteria();
		$criteria->add('test_version.description', 'test1');
		$criteria->add('test_version.title', 'test2');
		
		$criteria->add_select_column('wow');
				
		$this->assertIsA($this->test_db_table->select($criteria), 'Mockresult_set');

		$this->assertIsA($this->sql_builder->select_criteria, 'criteria');
		$this->assertIsA($this->sql_builder->connection, 'connection');
		
		$i = $this->sql_builder->select_criteria->get_iterator();
		$this->assertEqual($i->count(), 2);
		
		$this->assertEqual($this->sql_builder->select_criteria->get('test_version.description'), 'test1');
		$this->assertEqual($this->sql_builder->select_criteria->get('test_version.title'), 'test2');
		
		$columns = $this->sql_builder->select_criteria->get_select_columns();
		
		$this->assertEqual(sizeof($columns), 3);
		
		$this->assertTrue(in_array('id', $columns));
		$this->assertTrue(in_array('title', $columns));
		$this->assertTrue(in_array('description', $columns));
	}
		
	function test_select_all()
	{
		$this->assertIsA($this->test_db_table->select(), 'Mockresult_set');

		$this->assertIsA($this->sql_builder->select_criteria, 'criteria');
		$this->assertIsA($this->sql_builder->connection, 'connection');
		
		$i = $this->sql_builder->select_criteria->get_iterator();
		$this->assertEqual($i->count(), 0);
				
		$columns = $this->sql_builder->select_criteria->get_select_columns();
		
		$this->assertEqual(sizeof($columns), 3);
		
		$this->assertTrue(in_array('id', $columns));
		$this->assertTrue(in_array('title', $columns));
		$this->assertTrue(in_array('description', $columns));
	}
	
//	function test_delete()
//	{
//		$dependent_mock =& new test_dependent_db_table();
//		register_singleton_object($dependent_mock);
//		
//		$criteria = new criteria();
//		$criteria->add('test_version.description', 'test1');
//		$criteria->add('test_version.title', 'test2');
//		
//		$this->sql_builder->mock_rs->expectOnce('get_record_count');
//		$this->sql_builder->mock_rs->expectOnce('first');
//		$this->sql_builder->mock_rs->expectCallCount('next', 2);
//		$this->sql_builder->mock_rs->setReturnValueAt(0, 'next', true);
//		$this->sql_builder->mock_rs->setReturnValueAt(1, 'next', false);
//		$this->sql_builder->mock_rs->expectOnce('get_int', array('id'));
//		$this->sql_builder->mock_rs->setReturnValue('get_int', 10000);
//		$this->sql_builder->mock_rs->setReturnValue('get_record_count', 1);
//		
//		$dependent_criteria = new criteria();
//		$dependent_criteria->add('test_version.dependent', array(10000), criteria::IN());
//		
//		$this->assertTrue($this->test_db_table->delete($criteria));
//		
//		$this->assertIsA($this->sql_builder->delete_criteria, 'criteria');
//		$this->assertIsA($this->sql_builder->connection, 'connection');
//		
//		$i = $this->sql_builder->delete_criteria->get_iterator();
//		$this->assertEqual($i->count(), 2);
//		
//		$this->assertEqual($this->sql_builder->delete_criteria->get('test_version.description'), 'test1');
//		$this->assertEqual($this->sql_builder->delete_criteria->get('test_version.title'), 'test2');
//		
//		$this->assertEqual($dependent_mock->has_column_calls, 1);
//		$this->assertEqual($dependent_mock->delete_calls, 1);
//		
//		$this->assertTrue($dependent_criteria->equals($dependent_mock->delete_criteria));
//	}
	
	function test_delete2()
	{
		$dependent_mock =& new test_dependent_db_table($this);
		register_singleton_object($dependent_mock);
		
		$criteria = new criteria();
		$criteria->add('test_version.description', 'test1');
		$criteria->add('test_version.title', 'test2');
		
		$this->sql_builder->mock_rs->expectOnce('get_record_count');
		$this->sql_builder->mock_rs->expectOnce('first');
		$this->sql_builder->mock_rs->expectCallCount('next', 2);
		$this->sql_builder->mock_rs->setReturnValueAt(0, 'next', true);
		$this->sql_builder->mock_rs->setReturnValueAt(1, 'next', false);
		$this->sql_builder->mock_rs->expectOnce('get_int', array('id'));
		$this->sql_builder->mock_rs->setReturnValue('get_int', 10000);
		$this->sql_builder->mock_rs->setReturnValue('get_record_count', 1);
		
		$dependent_criteria = new criteria();
		$dependent_criteria->add('test_version.dependent', array(10000), criteria::IN());
		
		$dependent_mock->expectOnce('has_column', array('dependent'));
		$dependent_mock->setReturnValue('has_column', true);
		$dependent_mock->expectOnce('delete', array($dependent_criteria));
		
		$this->assertTrue($this->test_db_table->delete($criteria));
		
		$this->assertIsA($this->sql_builder->delete_criteria, 'criteria');
		$this->assertIsA($this->sql_builder->connection, 'connection');
		
		$i = $this->sql_builder->delete_criteria->get_iterator();
		$this->assertEqual($i->count(), 2);
		
		$this->assertEqual($this->sql_builder->delete_criteria->get('test_version.description'), 'test1');
		$this->assertEqual($this->sql_builder->delete_criteria->get('test_version.title'), 'test2');		
	}
	
//	function test_delete_all()
//	{
//		$dependent_mock =& new test_dependent_db_table();
//		register_singleton_object($dependent_mock);
//				
//		$this->sql_builder->mock_rs->expectOnce('get_record_count');
//		$this->sql_builder->mock_rs->expectOnce('first');
//		$this->sql_builder->mock_rs->expectCallCount('next', 2);
//		$this->sql_builder->mock_rs->setReturnValueAt(0, 'next', true);
//		$this->sql_builder->mock_rs->setReturnValueAt(1, 'next', false);
//		$this->sql_builder->mock_rs->expectOnce('get_int', array('id'));
//		$this->sql_builder->mock_rs->setReturnValue('get_int', 10000);
//		$this->sql_builder->mock_rs->setReturnValue('get_record_count', 1);
//		
//		$dependent_criteria = new criteria();
//		$dependent_criteria->add('test_version.dependent', array(10000), criteria::IN());
//		
//		$this->assertTrue($this->test_db_table->delete());
//		
//		$this->assertIsA($this->sql_builder->delete_criteria, 'criteria');
//		$this->assertIsA($this->sql_builder->connection, 'connection');
//				
//		$this->assertEqual($dependent_mock->has_column_calls, 1);
//		$this->assertEqual($dependent_mock->delete_calls, 1);
//		
//		$this->assertTrue($dependent_criteria->equals($dependent_mock->delete_criteria));
//	}
} 
?>