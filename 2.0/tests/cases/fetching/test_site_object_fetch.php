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
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');
require_once(TEST_CASES_DIR . '/fetching/site_object_fetch_test_init.php');

class test_site_object_fetch extends UnitTestCase 
{ 
	var $init_class_name = 'site_object_fetch_test_init';

	var $test_init = null;
	var $class_id = null;
	var $object = null;
	
  function test_site_object_fetch() 
  {
  	parent :: UnitTestCase();
  }

  function setUp()
  {
  	debug_mock :: init($this);

  	$this->test_init = new $this->init_class_name();
		
  	$this->_init_object();
  	$this->class_id = $this->object->get_class_id();

  	$this->test_init->init($this->object);
  }

  function _init_object()
  {
  	$this->object = new site_object_fetch_test();
  }
  
  function tearDown()
  { 
  	$this->test_init->_clean_up();
  
		debug_mock :: tally();
  }
  
  function test_fetch_ids_no_params()
  {
  	for($i = 1; $i <= 10; $i++)
  		$ids_array[] = $i;
  		
  	$result_ids = $this->object->fetch_ids();
  	
  	$this->assertEqual($result_ids , $ids_array);
  }
  
  function test_fetch_ids_no_class_restriction()
  {
  	for($i = 1; $i <= 20; $i++)
  		$ids_array[] = $i;
  		
  	$params = array('restrict_by_class' => false);	
  	$result_ids = $this->object->fetch_ids($params);
  	
  	$this->assertEqual($result_ids , $ids_array);
  }

  function test_fetch_ids_limit()
  {
 		$ids_array = array(4, 5, 6);
  		
  	$params = array('limit' => 3, 'offset' => 3);	
  	$result_ids = $this->object->fetch_ids($params);
  	
  	$this->assertEqual($result_ids , $ids_array);
  }

  function test_fetch_ids_limit_sort()
  {
 		$ids_array = array(6, 5, 4);
  		
  	$params = array('limit' => 3, 'offset' => 3, 'order' => array('title' =>  'DESC'));	
  	$result_ids = $this->object->fetch_ids($params);
  	
  	$this->assertEqual($result_ids , $ids_array);
  }

  function test_fetch_no_params()
  {
  	$result = $this->object->fetch();
  	
  	for($i = 1; $i <=10; $i++)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  	}
  }

  function test_fetch_limit_offset()
  {
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $limit = 3;
  	$result = $this->object->fetch($params);
  	
  	for($i = 4; $i <=6; $i++)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  	}
  }
  
  function test_fetch_limit_offset_order()
  {
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $limit = 3;
  	$params['order'] = array('title' => 'DESC');
  	$result = $this->object->fetch($params);
  	
  	for($i = 6; $i >=4; $i--)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  	}
  }
  
	function test_fetch_count_no_class_restriction()
	{
  	$params = array('restrict_by_class' => false);
  	$result = $this->object->fetch_count($params);
  	$this->assertEqual($result, 20);
	}

  function test_fetch_by_ids_no_ids()
  {
		debug_mock :: expect_write_error('ids array is empty');
  	$result = $this->object->fetch_by_ids(array());
  	$this->assertEqual($result, array());
  }
  
  function test_fetch_by_ids_no_params()
  {
  	$ids_array = array(1, 2, 4, 6, 9);
  	$result = $this->object->fetch_by_ids($ids_array);
  	
  	$this->assertEqual(array_keys($result), $ids_array);
  	
  	foreach($ids_array as $key)
  	{
  		$this->assertEqual($result[$key]['identifier'], 'object_' . $key);
  		$this->assertEqual($result[$key]['title'], 'object_' . $key . '_title');
  		$this->assertEqual($result[$key]['class_id'], $this->class_id);
  		$this->assertEqual($result[$key]['class_name'], get_class($this->object));
  	}
  }

  function test_fetch_by_ids_no_class_restriction()
  {
  	$params = array('restrict_by_class' => false);
  	$ids_array = array(12, 13, 15);

  	$result = $this->object->fetch_by_ids($ids_array, $params);
  	
  	$this->assertEqual(array_keys($result), $ids_array);
  }

  function test_fetch_by_ids_no_params_wrong_ids()
  {
  	$ids_array = array(1, 2, 4, 6, 9, 12, 13, 15);
  	$result = $this->object->fetch_by_ids($ids_array);
  	
  	$ids_array = array(1, 2, 4, 6, 9);
  	$this->assertEqual(array_keys($result), $ids_array);
  }
  
  function test_fetch_by_ids_limit()
  {
  	$ids_array = array(1, 2, 3, 4, 6, 8, 9);
  	$params['limit'] = $limit = 3;
  	
  	$result = $this->object->fetch_by_ids($ids_array, $params);
  	
  	$this->assertEqual(sizeof($result), $limit);
  }

  function test_fetch_by_ids_limit_offset()
  {
  	$ids_array = array(1, 2, 3, 4, 6, 8, 9);
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $offset = 2;
  	
  	$result = $this->object->fetch_by_ids($ids_array, $params);
  	
  	$this->assertEqual(array_keys($result), array(3, 4, 6));
  }
  
  function test_fetch_by_ids_order()
  {
  	$ids_array = array(1, 2, 3);
  	$params['order'] = array('sso.title' =>  'DESC');
  	
  	$result = $this->object->fetch_by_ids($ids_array, $params);
  	
  	$this->assertEqual(array_keys($result), array(3, 2, 1));
  }

  function test_fetch_by_ids_order_limit_offset()
  {
  	$ids_array = array(1, 2, 3, 4, 6, 8, 9);
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $offset = 2;
  	$params['order'] = array('sso.title' =>  'DESC');
  	
  	$result = $this->object->fetch_by_ids($ids_array, $params);
  	
  	$this->assertEqual(array_keys($result), array(6, 4, 3));
  }
  
  function test_fetch_by_ids_count()
  {
  	$ids_array = array(1, 2, 3, 4, 9);
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $offset = 2;
  	$result = $this->object->fetch_by_ids_count($ids_array, $params);
  	
  	$this->assertEqual($result, 5);
  }

  function test_fetch_by_ids_count_fake_params()
  {
  	$ids_array = array(1, 2, 3, 4, 9);
  	$params['limit'] = 3;
  	$params['offset'] = 2;
  	$result = $this->object->fetch_by_ids_count($ids_array, $params);
  	
  	$this->assertEqual($result, 5);
  }

  function test_fetch_by_ids_count_no_class_restriction()
  {
  	$ids_array = array(1, 2, 3, 4, 9, 12, 15, 17);
  	$params = array('restrict_by_class' => false);
  	$result = $this->object->fetch_by_ids_count($ids_array, $params);
  	
  	$this->assertEqual($result, 8);
  }

	function test_fetch_count_no_params()
	{
  	$result = $this->object->fetch_count();
  	$this->assertEqual($result, 10);
	}		
}

?>
