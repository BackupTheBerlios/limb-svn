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

require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');

class site_object_fetch_test_version extends site_object
{		
	function _define_attributes_definition()
	{
		return complex_array :: array_merge(
				parent :: _define_attributes_definition(),
				array(
				'title' => '',
				'name' => array('type' => 'numeric'),
				'search' => array('search' => true),
				));		
	}
	
	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 1,
			'db_table_name' => 'site_object',
			'controller_class_name' => 'controller_test'
		);
	}
}

class site_object_fetch_test_init
{ 
	var $db = null;
	var $class_id = '';
	var $root_node_id = '';
	
  function site_object_fetch_test_init() 
  {
		$this->db = db_factory :: instance();

		$this->_clean_up();
  }

  function init(& $object)
  {
  	$this->class_id = $object->get_class_id();
  	
  	$this->_insert_sys_site_object_records();
  	$this->_insert_fake_sys_site_object_records();
  }

  function _insert_sys_site_object_records()
  {
  	$db_table =& db_table_factory :: instance('sys_site_object');
  	
  	$tree =& tree :: instance();
		$values['identifier'] = 'root';
		$this->root_node_id = $tree->create_root_node($values, false, true);

  	$data = array();
  	for($i = 1; $i <= 10; $i++)
  	{
  		$version = mt_rand(1, 3);
  		$this->_insert_object_version_records($i, $version);
  		
  		$this->db->sql_insert('sys_site_object', 
  			array(
  				'id' => $i,
  				'class_id' => $this->class_id,
  				'current_version' => $version,
  				'identifier' => 'object_' . $i,
  				'title' => 'object_' . $i . '_title',
  				'status' => 0,
  				'locale_id' => 'en',
  			)
  		);

			$values['identifier'] = 'object_' . $i;
			$values['object_id'] = $i;
			$tree->create_sub_node($this->root_node_id, $values);
  	}
  }

  function _insert_object_version_records($object_id, $version_max)
  {
	}
	
  function _insert_fake_sys_site_object_records()
  {
  	$class_db_table = db_table_factory :: instance('sys_class');
  	$class_db_table->insert(array('id' => 1001, 'class_name' => 'fake_class'));
  	
  	$db_table =& db_table_factory :: instance('sys_site_object');

  	$tree =& tree :: instance();
  	
  	$data = array();
  	for($i = 11; $i <= 20 ; $i++)
  	{
  		$this->db->sql_insert('sys_site_object', 
  			array(
  				'id' => $i,
  				'class_id' => 1001,
  				'identifier' => 'object_' . $i,
  				'title' => 'object_' . $i . '_title',
  				'status' => 0,
  				'locale_id' => 'en',
  			)
  		);
  	
			$values['identifier'] = 'object_' . $i;
			$values['object_id'] = $i;
			$tree->create_sub_node($this->root_node_id, $values);
  	}
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_class');
  }
}

?>