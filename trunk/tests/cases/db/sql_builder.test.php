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
require_once(LIMB_DIR . '/core/lib/db/callable_statement.class.php');
require_once(LIMB_DIR . '/core/lib/db/result_set.class.php');
require_once(LIMB_DIR . '/core/lib/db/builder/criteria.class.php');
require_once(LIMB_DIR . '/core/lib/db/builder/sql_builder.class.php');
require_once(LIMB_DIR . '/core/lib/db/connection.class.php');
require_once(LIMB_DIR . '/core/lib/db/id_generator.class.php');

class sql_builder_test_db_table extends db_table
{    
  function _define_columns()
  {
  	return array(
  		'id' => array('type' => db_types::NUMERIC()),
      'description' => array('type' => db_types::VARCHAR()),
      'title' => array('type' => db_types::VARCHAR())
    );
  }  
}

class sql_builder_test_join_db_table extends db_table
{    
  function _define_columns()
  {
  	return array(
  		'id' => array('type' => db_types::NUMERIC()),
    );
  }  
}

Mock::generate('connection');
Mock::generate('callable_statement');
Mock::generate('id_generator');
Mock::generate('result_set');

class test_sql_builder extends UnitTestCase
{
	var $connection = null;
	var $builder = null;

	function test_sql_builder()
	{
		parent :: UnitTestCase();
	} 
		
	function setUp()
	{ 		
		$this->connection =& new Mockconnection($this);
		$this->builder =& new sql_builder();
	} 
	
	function tearDown()
	{
		$this->connection->tally();
	}
			
	function test_insert()
	{
		$id_generator = new Mockid_generator($this);
		$stmt = new Mockcallable_statement($this);

		$expected_sql = 'INSERT INTO sql_builder_test (description,title) VALUES (?,?)';
		
		$criteria = new criteria();
		$criteria->add('sql_builder_test.description', 'test1');
		$criteria->add('sql_builder_test.title', 'test2');
				
		$this->connection->expectOnce('get_id_generator');
		$this->connection->expectOnce('prepare_statement', array($expected_sql));
		$this->connection->setReturnReference('get_id_generator', $id_generator);
		$this->connection->setReturnReference('prepare_statement', $stmt);
		
		$id_generator->expectOnce('is_before_insert');
		$id_generator->setReturnValue('is_before_insert', false);
		$id_generator->expectOnce('is_after_insert');
		$id_generator->setReturnValue('is_after_insert', true);
		$id_generator->expectOnce('get_id', array('id'));
		$id_generator->setReturnValue('get_id', 1000);
		
		$stmt->expectArgumentsAt(0, 'set_string', array(1, 'test1'));
		$stmt->expectArgumentsAt(1, 'set_string', array(2, 'test2'));
		$stmt->expectOnce('execute_update');
		
		$this->assertEqual($this->builder->do_insert($criteria, $this->connection), 1000, 'return value should be equal to expected new generated id');
		
		$stmt->tally();
		$id_generator->tally();
	}
	
	function test_update()
	{
		$stmt = new Mockcallable_statement($this);
		
		$select_criteria = new criteria();
		$select_criteria->add('sql_builder_test.description', 'test1');
		$select_criteria->add('sql_builder_test.title', 'test2');
		
		$update_criteria = new criteria();
		$update_criteria->add('sql_builder_test.description', 'test3');
		
		$expected_sql = 'UPDATE sql_builder_test SET description=? WHERE sql_builder_test.description=? AND sql_builder_test.title=?';
		
		$this->connection->expectOnce('prepare_statement', array($expected_sql));
		$this->connection->setReturnReference('prepare_statement', $stmt);
		
		$stmt->expectOnce('execute_update');
		$stmt->expectOnce('close');
		$stmt->expectArgumentsAt(0, 'set_string', array(1, 'test3'));
		$stmt->expectArgumentsAt(1, 'set_string', array(2, 'test1'));
		$stmt->expectArgumentsAt(2, 'set_string', array(3, 'test2'));
		
		$this->assertTrue($this->builder->do_update($select_criteria, $update_criteria, $this->connection));
		
		$stmt->tally();
	}
	
	function test_select_no_join()
	{
		$stmt = new Mockcallable_statement($this);
		$rs = new Mockresult_set($this);
		
		$select_criteria = new criteria();
		$select_criteria->set_distinct();//this should not be used
		$select_criteria->add_select_column('sql_builder_test.description');
		$select_criteria->add_as_column('yo', 'sql_builder_test.description');
		$select_criteria->add_alias('sbt', 'sql_builder_test');//this should not be used
		$select_criteria->add('sql_builder_test.description', 'test1');
		$select_criteria->add('sql_builder_test.title', 'test2');
		$select_criteria->add_group_by_column('sql_builder_test.title');
		$select_criteria->add_ascending_order_by_column('sql_builder_test.title');
		$select_criteria->add_descending_order_by_column('sql_builder_test.description');
		
		$having = $select_criteria->get_new_criterion('sql_builder_test.title', 'Wow', criteria::EQUAL());
		$select_criteria->add_having($having);

		$expected_sql = 
		'SELECT sql_builder_test.description, sql_builder_test.description AS yo ' . 
		'FROM sql_builder_test ' . 
		'WHERE sql_builder_test.description=? AND sql_builder_test.title=? ' . 
		'ORDER BY sql_builder_test.title ASC,sql_builder_test.description DESC ' . 
		'GROUP BY sql_builder_test.title ' . 
		'HAVING sql_builder_test.title=?';
		
		$this->connection->expectOnce('prepare_statement', array($expected_sql));
		$this->connection->setReturnReference('prepare_statement', $stmt);
		
		$stmt->expectOnce('execute_query', array(result_set::FETCHMODE_ASSOC()));
		$stmt->setReturnReference('execute_query', $rs);
		$stmt->expectOnce('set_limit', array(0));
		$stmt->expectOnce('set_offset', array(0));
		
		$rs = $this->builder->do_select($select_criteria, $this->connection);
		
		$this->assertIsA($rs, 'Mockresult_set');
		
		$stmt->tally();
	}
	
	function test_select_join()
	{
		$stmt = new Mockcallable_statement($this);
		$rs = new Mockresult_set($this);
		
		$select_criteria = new criteria();
		$select_criteria->add_select_column('sql_builder_test.description');
		$select_criteria->add('sql_builder_test.description', 'test1');
		$select_criteria->add('sql_builder_test.title', 'test2');
		
		$select_criteria->add_join('sql_builder_test.id', 'sql_builder_test_join.id');
		
		$expected_sql = 
		'SELECT sql_builder_test.description ' . 
		'FROM sql_builder_test, sql_builder_test_join ' . 
		'WHERE sql_builder_test.description=? AND sql_builder_test.title=? AND sql_builder_test.id=sql_builder_test_join.id';
		
		$this->connection->expectOnce('prepare_statement', array($expected_sql));
		$this->connection->setReturnReference('prepare_statement', $stmt);
		
		$stmt->expectOnce('execute_query', array(result_set::FETCHMODE_ASSOC()));
		$stmt->setReturnReference('execute_query', $rs);
		$stmt->expectOnce('set_limit', array(0));
		$stmt->expectOnce('set_offset', array(0));
		
		$rs = $this->builder->do_select($select_criteria, $this->connection);
		
		$this->assertIsA($rs, 'Mockresult_set');
		
		$stmt->tally();
	}
	
	function test_delete()
	{
		$stmt = new Mockcallable_statement($this);
		$rs = new Mockresult_set($this);
		
		$select_criteria = new criteria();
		$select_criteria->add('sql_builder_test.description', 'test1');
		$select_criteria->add('sql_builder_test.title', 'test2');
		
		$expected_sql = 
		'DELETE FROM sql_builder_test ' . 
		'WHERE sql_builder_test.description=? AND sql_builder_test.title=?';
		
		$this->connection->expectOnce('prepare_statement', array($expected_sql));
		$this->connection->setReturnReference('prepare_statement', $stmt);
		
		$stmt->expectOnce('execute_update');
		$stmt->setReturnValue('execute_update', true);
		$stmt->expectArgumentsAt(0, 'set_string', array(1, 'test1'));
		$stmt->expectArgumentsAt(1, 'set_string', array(2, 'test2'));
		
		$this->assertTrue($this->builder->do_delete($select_criteria, $this->connection));
		
		$stmt->tally();
	}

} 
?>