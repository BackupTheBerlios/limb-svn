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
require_once(dirname(__FILE__) . '/site_object_fetch_test.class.php');
require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'class/core/access_policy.class.php');

class site_object_fetch_accessible_test_init extends site_object_fetch_test_init
{ 
  function site_object_fetch_accessible_test_init() 
  {
  	parent :: site_object_fetch_test_init();
  }

  function init(& $object)
  {
  	parent :: init($object);

  	$this->_insert_group_site_object_access_records();
  	$this->_insert_user_site_object_access_records();
  }

  function _clean_up()
  {
  	parent :: _clean_up();
  	
  	$this->db->sql_delete('sys_object_access');
  }

  function _insert_group_site_object_access_records()
  {
  	$access_db_table =& db_table_factory :: instance('sys_object_access');
  	
  	$data = array();
  	for($i = 1; $i <= 5; $i++)
  	{
  		$this->db->sql_insert('sys_object_access', 
  			array(
  				'object_id' => $i,
  				'accessor_id' => 100,
  				'r' => 1,
  				'w' => 1,
  				'accessor_type' => ACCESSOR_TYPE_GROUP,
  			)
  		);

  		$this->db->sql_insert('sys_object_access', 
  			array(
  				'object_id' => $i,
  				'accessor_id' => 110,
  				'r' => 1,
  				'w' => 0,
  				'accessor_type' => ACCESSOR_TYPE_GROUP,
  			)
  		);
  	}
  }

  function _insert_user_site_object_access_records()
  {
  	$access_db_table =& db_table_factory :: instance('sys_object_access');
  	
  	$data = array();
  	for($i = 8; $i <= 10; $i++)
  	{
  		$this->db->sql_insert('sys_object_access', 
  			array(
  				'object_id' => $i,
  				'accessor_id' => 200,
  				'r' => 1,
  				'w' => 1,
  				'accessor_type' => ACCESSOR_TYPE_USER,
  			)
  		);
  	}
  }
}

?>