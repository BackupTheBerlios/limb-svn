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
require_once(LIMB_DIR . '/core/lib/util/complex_array.class.php');

class project_db_tables_test extends UnitTestCase
{
	var $db = null;
	var $db_tables = array();
	var $db_tables_names = array();
	
	function project_db_tables_test($name = 'db tables test case')
	{
		$this->db =& db_factory :: instance();
		
		parent :: UnitTestCase($name);
	} 
	
	function db_test_tables()
	{
		$this->_load_db_tables(PROJECT_DIR . '/core/db_tables/');
		$this->_load_db_tables(LIMB_DIR . '/core/db_tables/');
		
		$this->_check_all_data_bases();
	}
	
	function _check_all_data_bases()
	{
		$project_db = str_replace('_tests', '', DB_NAME);
		
		$dbs = array_unique(array(DB_NAME, $project_db));
		
		foreach($dbs as $db)
		{
			$this->db->select_db($db);
			$this->_check_db_tables();
		}
		
		$this->db->select_db(DB_NAME);
	}
	
	function _check_db_tables()
	{
		foreach($this->db_tables as $db_table)
		{
			$this->_check_db_table($db_table);
			
			$this->_check_constraints($db_table);
		}
	}
		
	function _check_db_table($db_table)
	{
		$table_name = $db_table->get_table_name();
		$primary_key_name = $db_table->get_primary_key_name();
		$columns = $db_table->get_columns();
		
		$this->db->sql_exec('SHOW TABLES LIKE "' . $table_name . '"');
		
		if(!$res = $this->db->fetch_row()) //???
			return;
					
		$this->assertEqual(sizeof($res), 1, $table_name . ' doesnt exist in db');

		$this->db->sql_exec('SHOW COLUMNS FROM ' . $table_name);
		$db_columns = $this->db->get_array();
		
		$a1 = complex_array :: get_column_values('Field', $db_columns);
		$a2 = array_keys($columns);
		
		sort($a1);
		sort($a2);
		
		$this->assertEqual(sizeof($db_columns), sizeof($columns), 
			$table_name . ' has wrong number of columns: db columns: ' . 
			var_export($a1, true) . 
			' against ' .  var_export($a2, true) . '');
					
		$db_primary_key_name = '';
		foreach($db_columns as $db_column)
		{
			if($db_column['Key'] == 'PRI')
				$db_primary_key_name = $db_column['Field'];
			
			$status_string = 'db table:"' . $table_name . '"  field definition:"' . $db_column['Field'] . ' does not exist in class';
			
			$this->assertTrue(in_array($db_column['Field'], array_keys($columns)), $status_string);
			
			$type = $db_table->get_column_type($db_column['Field']);
			
			$status_string = 'db table:"' . $table_name . '"  column:"' . $db_column['Field']. '"  db type: "' . $db_column['Type'] . '" expected: ';
			
			switch($type)
			{
				case '':
				case 'string':	
					$this->assertWantedPattern('/char|text/', $db_column['Type'], $status_string . 'string');
				break;
				
				case 'blob':
					$this->assertWantedPattern('/blob/', $db_column['Type'], $status_string . 'blob');
				break;
				
				case 'numeric':
					$this->assertWantedPattern('/int|double|dec|float/', $db_column['Type'], $status_string . 'numeric');
				break;
				
				case 'date':
					$this->assertWantedPattern('/date/', $db_column['Type'], $status_string . 'date');
				break;
				
				default:
					$this->assertTrue(false, 'Unknown type: ' . $status_string);
			}				
		}
		
		if($primary_key_name)
		{
			$this->assertEqual($db_primary_key_name, $primary_key_name, 'table ' . $table_name . ': '. $primary_key_name . ' primary key not found');
		}
	}
	
	function _check_constraints($db_table)
	{
		$constraints = $db_table->get_constraints();
		$table_name = $db_table->get_table_name();
		
		foreach($constraints as $column => $constraint_data)
		{
			$columns = $db_table->get_columns();
			
			$this->assertTrue(in_array($column, array_keys($columns)), 'table ' . $table_name . ': contstraint column doesnt exist: "' . $column .'"');
			
			foreach($constraint_data as $data)
			{
				$constraint_table = $data['table_name'];
				$field = $data['field'];
				
				$index = array_search($constraint_table, $this->db_tables_names);
				
				$this->assertNotIdentical($index, false, 'table ' . $table_name . ': contstraint table doesnt exist: "' . $constraint_table .'"');
				
				$constraint_table_columns = $this->db_tables[$index]->get_columns();
				
				$this->assertTrue(in_array($field, array_keys($constraint_table_columns)), 'table "' . $constraint_table . '" : column doesnt exist: "' . $field .'"');
				
				$constraint_type = $this->db_tables[$index]->get_column_type($field);
				
				$column_type = $db_table->get_column_type($column);
				
				if($column_type == '' || $column_type == 'string')
					$this->assertTrue(($constraint_type == '' || $constraint_type = 'string'), 'table "' . $constraint_table . '" : column type of "' . $field . '" doesnt match to "' . $column . '" of "'  . $table_name . '"');
				else
					$this->assertEqual($constraint_type, $column_type, 'table "' . $constraint_table . '" : column type of "' . $field . '" doesnt match to "' . $column . '" of "'  . $table_name . '"');
			}
		}
	}
	
	function _load_db_tables($dir_name)
	{
		if ($dir = @opendir($dir_name))
		{  
			while(($object_file = readdir($dir)) !== false) 
			{  
				if  (substr($object_file, -10,  10) == '.class.php')
				{
					$class_name = substr($object_file, 0, strpos($object_file, '.'));
					
					$this->assertIdentical(array_search($class_name, array_keys($this->db_tables)), false, 'collision with db table class(already included): "' . $class_name . '" in directory: "' . $dir_name . '"');
					
					include_once($dir_name . '/' . $object_file);
					
					$db_table = new $class_name();
					$this->db_tables[$class_name] = $db_table;
					$db_table_name = $db_table->get_table_name();
					
					if(array_search($db_table_name, $this->db_tables_names) !== false)
						unset($this->db_tables[$class_name]);
					else
						$this->db_tables_names[$class_name] = $db_table_name;
						
				} 
			} 
			closedir($dir); 
		} 
	}
		
} 
?>