<?php

require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/lib/db/db_mysql.class.php');

Mock::generatePartial
(
  'db_mysql',
  'test_db_mysql_version',
  array(
  	'_sql_string_insert',
  	'sql_exec'
  )
); 


class test_db_mysql_typecast extends UnitTestCase
{
	var $mock_db_module = null;
	
	function test_db_mysql_typecast($name = 'mysql db test case')
	{
		parent :: UnitTestCase($name);
	} 
		
	function setUp()
	{ 
		$this->mock_db_module =& new test_db_mysql_version($this);		
	} 
	
	function tearDown()
	{ 
		$this->mock_db_module->tally();
	}
	
	function test_insert_default_types()
	{
		$this->mock_db_module->expectOnce('_sql_string_insert', 
			array(
				'test', 
				array(
					'id' => "'1'",
					'title' => "' \\\"\\\" title\''",
					'null' => "''",
					'bool_true' => 1,
					'bool_false' => 0
				)
			));
		
		$this->mock_db_module->expectOnce('sql_exec');
		
		$this->mock_db_module->sql_insert('test', 
			array(
				'id' => 1, 
				'title' => " \"\" title'",
				'null' => null,
				'bool_true' => true,
				'bool_false' => false
			)
		);
	} 
	
	function test_insert_defined_types()
	{
		$this->mock_db_module->expectOnce('_sql_string_insert', 
			array(
				'test', 
				array(
					'id' => 0,
					'id1' => 1000,
					'date_not_iso' => '\'1982-12-01\'',
					'date_iso' => '\'1982-12-01\'',
					'datetime_not_iso' => '\'1982-12-01 12:01:59\'',
					'datetime_iso' => '\'1982-12-01 12:01:59\'',
					'title' => "' \\\"\\\" title\''",
				)
			));
		
		$this->mock_db_module->expectOnce('sql_exec');
		
		$this->mock_db_module->sql_insert('test', 
			array(
				'id' => 'abc zxc', 
				'id1' => '1000',
				'date_not_iso' => '12/01/1982',
				'date_iso' => '1982-12-01',
				'datetime_not_iso' => '12/01/1982 12:01:59',
				'datetime_iso' => '1982-12-01 12:01:59',
				'title' => " \"\" title'",
			),
			array(
				'id' => 'numeric',
				'id1' => 'numeric',
				'date_not_iso' => 'date',
				'date_iso' => 'date',
				'datetime_not_iso' => 'datetime',
				'datetime_iso' => 'datetime',
				'title' => 'string'
			)
		);
	} 

	function test_insert_defined_with_not_defined_types()
	{
		$this->mock_db_module->expectOnce('_sql_string_insert', 
			array(
				'test', 
				array(
					'null' => "''",
					'bool_true' => 1,
					'bool_false' => 0,
					'id' => "'abc zxc'",
					'id1' => 1000,
					'date_not_iso' => '\'1982-12-01\'',
					'date_iso' => '\'1982-12-01\'',
					'datetime_not_iso' => '\'1982-12-01 12:01:59\'',
					'datetime_iso' => '\'1982-12-01 12:01:59\'',
					'title' => "' \\\"\\\" title\''",
				)
			));
		
		$this->mock_db_module->expectOnce('sql_exec');
		
		$this->mock_db_module->sql_insert('test', 
			array(
				'null' => null,
				'bool_true' => true,
				'bool_false' => false,
				'id' => 'abc zxc', 
				'id1' => '1000',
				'date_not_iso' => '12/01/1982',
				'date_iso' => '1982-12-01',
				'datetime_not_iso' => '12/01/1982 12:01:59',
				'datetime_iso' => '1982-12-01 12:01:59',
				'title' => " \"\" title'",
			),
			array(
				'id1' => 'numeric',
				'date_not_iso' => 'date',
				'date_iso' => 'date',
				'datetime_not_iso' => 'datetime',
				'datetime_iso' => 'datetime',
				'title' => 'string'
			)
		);
	} 

} 
?>