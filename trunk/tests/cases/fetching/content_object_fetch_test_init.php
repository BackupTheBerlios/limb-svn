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

require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/db_tables/content_object_db_table.class.php');

require_once(LIMB_DIR . '/tests/cases/fetching/site_object_fetch_test_init.php');
require_once(LIMB_DIR . '/tests/cases/fetching/content_object_fetch_test_init.php');

class news_object_test_fetch_db_table extends content_object_db_table
{
	function news_object_test_fetch_db_table()
	{
		parent :: content_object_db_table();
	}
	
	function _define_db_table_name()
	{
		return 'news_object';
	}
	
  function _define_columns()
  {
  	return array(
      'annotation' => '',
      'content' => '',
      'news_date' => array('type' => 'date'),
    );
  }
}

class news_object_fetch_test extends content_object
{
	function news_object_fetch_test()
	{
		parent :: content_object();
	}
			
	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 1,
			'db_table_name' => 'news_object_test_fetch',
			'controller_class_name' => 'test_controller'
		);
	}
}

class content_object_fetch_test_init extends site_object_fetch_test_init
{ 
  function content_object_fetch_test_init() 
  {
  	parent :: site_object_fetch_test_init();
  }

  function init(& $object)
  {
  	parent :: init($object);

  	$this->_insert_content_object_records($object);
  }

  function _clean_up()
  {
  	parent :: _clean_up();
  	
  	$this->connection->sql_delete('sys_object_version');
  	$this->connection->sql_delete('news_object');
  }

  function _insert_content_object_records(& $object)
  {
  	$db_table =& $object->_get_db_table();

  	$sys_db_table =& db_table_factory :: instance('sys_site_object');
  	
  	$data = array();
  	$version_data = array();
  	for($i = 1; $i <= 10; $i++)
  	{
  		$record = $sys_db_table->get_row_by_id($i);
  		
  		for($k = $record['current_version']; $k <= $record['current_version']; $k++)
  		{
	  		$data['version'] = $k;
	  		$data['object_id'] = $i;
	  		$data['annotation'] = 'object_' . $i . '_annotation_version_' . $k;
	  		$data['news_date'] = '2003-01-02';
	  		$data['content'] = 'object_' . $i . '_content_version_' . $k;
	  		$data['identifier'] = 'object_' . $i;
	  		$data['title'] = 'object_' . $i . '_title';
	  		
	  		$db_table->insert($data);
  		}
  	}
  }
  
  function _insert_object_version_records($object_id, $version_max)
  {
  	$version_db_table =& db_table_factory :: instance('sys_object_version');
  	
  	for($i = 1; $i <= $version_max; $i++)
  	{
  		$data = array();
  		$data['version'] = $i;
  		$data['object_id'] = $object_id;
  		
  		$version_db_table->insert($data);
		}
  }
  
}

?>
