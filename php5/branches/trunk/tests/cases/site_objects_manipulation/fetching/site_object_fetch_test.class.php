<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object_factory.class.php');

class site_object_fetch_test extends LimbTestCase
{
	var $class_id;
	var $object;
	var $db;

	function site_object_fetch_test()
	{
	  parent :: LimbTestCase();

    $this->db = db_factory :: instance();
	}

	function __destruct()
	{
	  $this->_clean_up();
	}

  function setUp()
  {
    static $first_time = 1;

    if($first_time == 1)
    {
      $this->_clean_up();

    	$this->_init_object();
      $this->_init_fetch_data($this->object);

      $this->class_id = $this->object->get_class_id();
      $first_time++;
    }
  }

  function _init_fetch_data($object)
  {
    include_once(dirname(__FILE__) . '/site_object_fetch_test_init.php');
    $test_init = new site_object_fetch_test_init();
  	$test_init->init($object);
  }

  function _init_object()
  {
  	$this->object = new site_object();
  }

  function tearDown()
  {
  }

  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_class');
  	$this->db->sql_delete('sys_behaviour');
  }

  function test_fetch_ids_no_params()
  {
  	for($i = 1; $i <= 5; $i++)
  		$ids_array[] = $i;

  	$result_ids = $this->object->fetch_ids();

  	$this->assertEqual($result_ids , $ids_array);
  }

  function test_fetch_ids_no_class_restriction()
  {
  	for($i = 1; $i <= 10; $i++)
  		$ids_array[] = $i;

  	$params = array('restrict_by_class' => false);
  	$result_ids = $this->object->fetch_ids($params);

  	$this->assertEqual($result_ids , $ids_array);
  }

  function test_fetch_ids_limit()
  {
 		$ids_array = array(3, 4, 5);

  	$params = array('limit' => 3, 'offset' => 2);
  	$result_ids = $this->object->fetch_ids($params);

  	$this->assertEqual($result_ids , $ids_array);
  }

  function test_fetch_ids_limit_sort()
  {
 		$ids_array = array(3, 2, 1);

  	$params = array('limit' => 3, 'offset' => 2, 'order' => array('title' =>  'DESC'));
  	$result_ids = $this->object->fetch_ids($params);

  	$this->assertEqual($result_ids , $ids_array);
  }

  function test_fetch_no_params()
  {
  	$result = $this->object->fetch();

  	for($i = 1; $i <=5; $i++)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  		$this->assertEqual($result[$i]['behaviour'], 'site_object_behaviour');
  	}
  }

  function test_fetch_limit_offset()
  {
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $limit = 2;
  	$result = $this->object->fetch($params);

  	for($i = 3; $i <= 5; $i++)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  		$this->assertEqual($result[$i]['behaviour'], 'site_object_behaviour');
  	}
  }

  function test_fetch_limit_offset_order()
  {
  	$params['limit'] = $limit = 3;
  	$params['offset'] = 2;
  	$params['order'] = array('title' => 'DESC');
  	$result = $this->object->fetch($params);

  	for($i = 3; $i >=1; $i--)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  		$this->assertEqual($result[$i]['behaviour'], 'site_object_behaviour');
  	}
  }

	function test_fetch_count_no_class_restriction()
	{
  	$params = array('restrict_by_class' => false);
  	$result = $this->object->fetch_count($params);
  	$this->assertEqual($result, 10);
	}

  function test_fetch_by_ids_no_ids()
  {
  	$result = $this->object->fetch_by_ids(array());
  	$this->assertEqual($result, array());
  }

  function test_fetch_by_ids_no_params()
  {
  	$ids_array = array(1, 2, 4, 5);
  	$result = $this->object->fetch_by_ids($ids_array);

  	$keys = array_keys($result);
  	sort($keys);

  	$this->assertEqual($keys, $ids_array);

  	foreach($ids_array as $key)
  	{
  		$this->assertEqual($result[$key]['identifier'], 'object_' . $key);
  		$this->assertEqual($result[$key]['title'], 'object_' . $key . '_title');
  		$this->assertEqual($result[$key]['class_id'], $this->class_id);
  		$this->assertEqual($result[$key]['class_name'], get_class($this->object));
  		$this->assertEqual($result[$key]['behaviour'], 'site_object_behaviour');
  	}
  }

  function test_fetch_by_ids_no_class_restriction()
  {
  	$params = array('restrict_by_class' => false);
  	$ids_array = array(6, 8, 9);

  	$result = $this->object->fetch_by_ids($ids_array, $params);

  	$keys = array_keys($result);
  	sort($keys);

  	$this->assertEqual($keys, $ids_array);
  }

  function test_fetch_by_ids_no_params_wrong_ids()
  {
  	$ids_array = array(1, 2, 4, 5, 7, 8, 10);
  	$result = $this->object->fetch_by_ids($ids_array);

  	$ids_array = array(1, 2, 4, 5);

  	$keys = array_keys($result);
  	sort($keys);

  	$this->assertEqual($keys, $ids_array);
  }

  function test_fetch_by_ids_limit()
  {
  	$ids_array = array(1, 2, 3, 4, 5);
  	$params['limit'] = $limit = 3;

  	$result = $this->object->fetch_by_ids($ids_array, $params);

  	$this->assertEqual(sizeof($result), $limit);
  }

  function test_fetch_by_ids_limit_offset()
  {
  	$ids_array = array(1, 2, 3, 4, 5);
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $offset = 2;
  	$params['order'] = array('sso.title' =>  'ASC');

  	$result = $this->object->fetch_by_ids($ids_array, $params);

  	$keys = array_keys($result);
  	sort($keys);

  	$this->assertEqual($keys, array(3, 4, 5));
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
  	$ids_array = array(1, 2, 3, 5);
  	$params['limit'] = $limit = 2;
  	$params['offset'] = $offset = 2;
  	$params['order'] = array('sso.title' =>  'DESC');

  	$result = $this->object->fetch_by_ids($ids_array, $params);

  	$this->assertEqual(array_keys($result), array(2, 1));
  }

  function test_fetch_by_ids_count_fake_params()
  {
  	$ids_array = array(1, 2, 3, 4, 5);
  	$params['limit'] = 3;
  	$params['offset'] = 2;
  	$result = $this->object->fetch_by_ids_count($ids_array, $params);

  	$this->assertEqual($result, 5);
  }

  function test_fetch_by_ids_count_no_class_restriction()
  {
  	$ids_array = array(1, 2, 3, 4, 7, 8, 10);
  	$params = array('restrict_by_class' => false);
  	$result = $this->object->fetch_by_ids_count($ids_array, $params);

  	$this->assertEqual($result, 7);
  }

	function test_fetch_count_no_params()
	{
  	$result = $this->object->fetch_count();
  	$this->assertEqual($result, 5);
	}
}

?>