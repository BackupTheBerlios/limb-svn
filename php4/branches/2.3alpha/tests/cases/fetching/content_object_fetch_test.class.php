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

require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/model/site_objects/content_object.class.php');
require_once(LIMB_DIR . '/tests/cases/fetching/content_object_fetch_test_init.php');

class content_object_fetch_test extends site_object_fetch_test 
{ 
	var $init_class_name = 'content_object_fetch_test_init';
	
  function _init_object()
  {
  	$this->object = new news_object_fetch_test();
  	$this->class_id = $this->object->get_class_id();
  }
  
  function test_fetch_ids_no_class_restriction()
  {
  	for($i = 1; $i <= 10; $i++)
  		$ids_array[] = $i;
  		
  	$params = array('restrict_by_class' => false);	
  	$result_ids = $this->object->fetch_ids($params);
  	
  	sort($result_ids);
  	
  	$this->assertEqual($result_ids , $ids_array);
  }

  function test_fetch_ids_limit_sort()
  {
 		$ids_array = array(6, 5, 4);
  		
  	$params = array('limit' => 3, 'offset' => 3, 'order' => array('annotation' =>  'DESC'));	
  	$result_ids = $this->object->fetch_ids($params);
  	
  	$this->assertEqual($result_ids , $ids_array);
  }

	function test_fetch_count_no_class_restriction()
	{
  	$result = $this->object->fetch_count();
  	$params = array('restrict_by_class' => false);
  	$this->assertEqual($result, 10);
	}		

  function test_fetch_limit_offset_order()
  {
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $limit = 3;
  	$params['order'] = array('annotation' => 'DESC');
  	$result = $this->object->fetch($params);
  	
  	for($i = 6; $i >=4; $i--)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  	}
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
  		$this->assertEqual($result[$key]['current_version'], $result[$key]['version']);
  		$version = $result[$key]['current_version'];
  		$this->assertEqual($result[$key]['annotation'], 'object_' . $key . '_annotation_version_' . $version);
  		$this->assertEqual($result[$key]['content'], 'object_' . $key . '_content_version_' . $version);
  		$this->assertEqual($result[$key]['class_name'], get_class($this->object));
  	}
  }

  function test_fetch_by_ids_no_params_wrong_ids()
  {
  	$ids_array = array(1, 2, 4, 6, 9, 12, 13, 15);
  	$result = $this->object->fetch_by_ids($ids_array);
  	
  	$ids_array = array(1, 2, 4, 6, 9);
  	$this->assertEqual(array_keys($result), $ids_array);
  }

  function test_fetch_by_ids_no_class_restriction()
  {
  	$params = array('restrict_by_class' => false);
  	$ids_array = array(1, 2, 4, 6, 9, 12, 13, 15);
  	$result = $this->object->fetch_by_ids($ids_array, $params);
  	
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
  	$params['order'] = array('annotation' =>  'DESC');
  	
  	$result = $this->object->fetch_by_ids($ids_array, $params);
  	
  	$this->assertEqual(array_keys($result), array(3, 2, 1));
  }

  function test_fetch_by_ids_order_limit_offset()
  {
  	$ids_array = array(1, 2, 3, 4, 6, 8, 9);
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $offset = 2;
  	$params['order'] = array('annotation' =>  'DESC');
  	
  	$result = $this->object->fetch_by_ids($ids_array, $params);
  	
  	$this->assertEqual(array_keys($result), array(6, 4, 3));
  }

  function test_fetch_by_ids_count_no_class_restriction()
  {
  	$ids_array = array(1, 2, 3, 4, 9, 12, 15, 17);
  	$params = array('restrict_by_class' => false);
  	$result = $this->object->fetch_by_ids_count($ids_array, $params);
  	
  	$this->assertEqual($result, 5);
  }
}

?>