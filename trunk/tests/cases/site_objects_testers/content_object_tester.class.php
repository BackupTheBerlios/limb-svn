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
require_once(LIMB_DIR . '/tests/cases/site_objects_testers/site_object_tester.class.php');
require_once(LIMB_DIR . 'class/core/site_objects/content_object.class.php');

class content_object_tester extends site_object_tester 
{ 
  function content_object_tester($class_name) 
  {
  	parent :: site_object_tester($class_name);
  }
  
  function _clean_up()
  {
  	parent :: _clean_up();

  	$this->db->sql_delete('sys_object_version');
		
		$this->_clean_content_db_table_records();		
  }
  
  function _clean_content_db_table_records()
  {
		$db_table = $this->object->_get_db_table();
  	$this->db->sql_delete($db_table->get_table_name());
  }
  
  function _do_test_class_properties($props)
  {
  	parent :: _do_test_class_properties($props);
  	
		$db_table = $this->object->_get_db_table();

		if(isset($props['db_table_name']))
		{
			$this->assertTrue(is_a($db_table, $props['db_table_name'].'_db_table'));
		}
		else
		{
			$this->assertTrue(is_a($db_table, get_class($this->object).'_db_table'));
		}
  }
  
  function test_create()
  {
  	$this->_set_object_initial_attributes();
  	
  	parent :: test_create();

  	$this->_check_sys_object_version_record();

 		$this->_check_content_object_record();
  }
  
  function _generate_string()
  {
		$alphabet = array(
				array('b','c','d','f','g','h','g','k','l','m','n','p','q','r','s','t','v','w','x','z',
							'B','C','D','F','G','H','G','K','L','M','N','P','Q','R','S','T','V','W','X','Z'),
				array('a','e','i','o','u','y','A','E','I','O','U','Y'),
		);
		
		$string = '';
		for($i = 0; $i < 9 ;$i++)
		{
			$j = $i%2;
			$min_value = 0;
			$max_value = count($alphabet[$j]) - 1;
			$key = rand($min_value, $max_value);
			$string .= $alphabet[$j][$key];
		}
		
		return $string;
  }
  
  function _generate_number()
  {
  	return mt_rand(1, 100);
  }
  
  function _generate_test_attributes()
  {
  	$definition = $this->object->get_attributes_definition();
  	
  	foreach($this->object->get_attributes_definition() as $attribute => $data)
  	{
  		if(in_array($attribute, array('id', 'version', 'object_id', 'parent_node_id')))
  			continue;
  		
  		if($data === '' || (is_array($data) && !isset($data['type'])))
  			$type = 'string';
  		else
  			$type = $data['type'];
  		
			switch ($type)
			{
				case 'numeric':
					$this->object->set_attribute($attribute, $this->_generate_number());
				break;
				
				case 'string':
					$this->object->set_attribute($attribute, $this->_generate_number());
				break;
			}
			
  	}
  }
  
  function _set_object_initial_attributes()
  {
  	$this->_generate_test_attributes();
  }

	function _set_object_secondary_update_attributes()
	{
		$this->_generate_test_attributes();
	}
	
	function test_versioned_update()
	{
		$this->_test_update(true);
	}

	function test_unversioned_update()
	{
		$this->_test_update(false);
	}
	
  function _test_update($versioned = false)
  {
  	$this->_set_object_initial_attributes();
  	
  	$this->object->set_parent_node_id($this->parent_node_id);

  	$id = $this->object->create();
  	$node_id = $this->object->get_node_id();

  	$this->_set_object_secondary_update_attributes();
  	
  	$version = $this->object->get_version();
  	$result = $this->object->update($versioned);
  	
  	$this->assertTrue($result, 'update operation failed');

  	if ($versioned)
  		$this->assertEqual($version + 1, $this->object->get_version(), get_class($this->object) . ': version index is the same as before versioned update');
  	else	
  		$this->assertEqual($version, $this->object->get_version(), get_class($this->object) . ': version index schould be the same as before unversioned update');
  	
  	$this->_check_sys_site_object_tree_record();
  	
	 	$this->_check_sys_site_object_record();

 		$this->_check_sys_object_version_record();

 		$this->_check_content_object_record();
  }

  function test_delete()
  {
  	$this->_set_object_initial_attributes();
  	
  	parent :: test_delete();
  }

	function test_fetch()
	{
  	$this->_set_object_initial_attributes();
  	
  	parent :: test_fetch();
	}
		      
  function _check_sys_object_version_record()
	{
		$conditions['object_id'] = $this->object->get_id();
		$conditions['version'] = $this->object->get_version();
		
  	$this->db->sql_select('sys_object_version', '*', $conditions);
  	$record = $this->db->fetch_row();
  	
  	$user =& user :: instance();
  	
  	$this->assertEqual($record['object_id'], $this->object->get_id());
  	$this->assertEqual($record['version'], $this->object->get_version());
  	$this->assertEqual($record['creator_id'], $user->get_id());
	}	

  function _check_content_object_record()
	{
		$conditions['object_id'] = $this->object->get_id();
		$conditions['version'] = $this->object->get_version();

		$db_table = $this->object->_get_db_table();
		$arr = $db_table->get_list($conditions, 'id');

  	$this->assertEqual(sizeof($arr), 1);
  	$record = current($arr);
		
		$attribs = $this->object->export_attributes();
		
		foreach($attribs as $name => $value)
			if (isset($record[$name]) && !in_array($name, array('id', 'object_id')))
				$this->assertEqual($record[$name], $value, get_class($this->object) . ': db content is not valid at attribute "' . $name . '"');
	}		
}

?>