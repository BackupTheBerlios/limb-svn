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

class test_project_db_tables extends UnitTestCase
{
	var $db = null;
	var $db_tables = array();
	var $db_tables_names = array();
	
	function test_project_db_tables($name = 'db tables test case')
	{
		$this->db =& db_factory :: instance();
		
		parent :: UnitTestCase($name);
	} 
	
	function test_db_tables()
	{
		$this->_load_db_tables(LIMB_DIR . '/core/db_tables/');
		
		$this->_check_db_tables();
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
		
		$this->assertEqual(sizeof($db_columns), sizeof($columns), $table_name . ' has wrong number of columns');
					
		$db_primary_key_name = '';
		foreach($db_columns as $db_column)
		{
			if($db_column['Key'] == 'PRI')
				$db_primary_key_name = $db_column['Field'];
			
			$this->assertTrue(in_array($db_column['Field'], array_keys($columns)));
			
			$type = $db_table->get_column_type($db_column['Field']);
			
			$status_string = 'db table:"' . $table_name . '"  column:"' . $db_column['Field'] . '"  db type: "' . $db_column['Type'] . '"';
			
			switch($type)
			{
				case '':
				case 'string':	
					$this->assertWantedPattern('/char|text/', $db_column['Type'], $status_string);
				break;
				
				case 'blob':
					$this->assertWantedPattern('/blob/', $db_column['Type'], $status_string);
				break;
				
				case 'numeric':
					$this->assertWantedPattern('/int|double/', $db_column['Type'], $status_string);
				break;
				
				case 'date':
					$this->assertWantedPattern('/date/', $db_column['Type'], $status_string);
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
		if ($dir = opendir($dir_name))
		{  
			while(($object_file = readdir($dir)) !== false) 
			{  
				if  (substr($object_file, -10,  10) == '.class.php')
				{
					$class_name = substr($object_file, 0, strpos($object_file, '.'));
					
					if(!class_exists($class_name))
					{
						include_once($dir_name . '/' . $object_file);
						
						$db_table = new $class_name();
						$this->db_tables[] = $db_table;
						$this->db_tables_names[] = $db_table->get_table_name();
					}
				} 
			} 
			closedir($dir); 
		} 
	}
		
} 
?>