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
require_once(LIMB_DIR . '/class/core/site_objects/content_object.class.php');
require_once(dirname(__FILE__) . '/site_object_fetch_test.class.php');
require_once(LIMB_DIR . '/class/db_tables/content_object_db_table.class.php');

class news_object_fetch_test_db_table extends content_object_db_table
{
	function _define_db_table_name()
	{
		return 'test_news_object';
	}

  function _define_columns()
  {
  	return complex_array :: array_merge (
  	  parent :: _define_columns(),
  	  array(
        'annotation' => '',
        'content' => '',
        'news_date' => array('type' => 'date'),
      )
    );
  }
}

class news_object_fetch_test extends content_object
{
	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 1,
			'db_table_name' => 'news_object_fetch_test',
			'controller_class_name' => 'controller_test'
		);
	}
}

class content_object_fetch_test extends LimbTestCase
{
	var $class_id = null;
	var $object = null;
	var $db = null;

	function content_object_fetch_test()
	{
	  parent :: LimbTestCase();

    $this->db = db_factory :: instance();
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

  	debug_mock :: init($this);
  }

	function __destruct()
	{
	  $this->_clean_up();
	}

  function _init_object()
  {
  	$this->object = new news_object_fetch_test();
  	$this->class_id = $this->object->get_class_id();
  }

  function _init_fetch_data($object)
  {
    include_once(dirname(__FILE__) . '/content_object_fetch_test_init.php');
    $test_init = new content_object_fetch_test_init();
  	$test_init->init($object);
  }

  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_class');
  	$this->db->sql_delete('sys_object_version');
  	$this->db->sql_delete('test_news_object');
  }

  function test_fetch_ids_no_class_restriction()
  {
  	for($i = 1; $i <= 5; $i++)
  		$ids_array[] = $i;

  	$params = array('restrict_by_class' => false);
  	$result_ids = $this->object->fetch_ids($params);

  	sort($result_ids);

  	$this->assertEqual($result_ids , $ids_array);
  }

  function test_fetch_ids_limit_sort()
  {
 		$ids_array = array(3, 2, 1);

  	$params = array('limit' => 3, 'offset' => 2, 'order' => array('annotation' =>  'DESC'));
  	$result_ids = $this->object->fetch_ids($params);

  	$this->assertEqual($result_ids , $ids_array);
  }

	function test_fetch_count_no_class_restriction()
	{
  	$result = $this->object->fetch_count();
  	$params = array('restrict_by_class' => false);
  	$this->assertEqual($result, 5);
	}

  function test_fetch_limit_offset_order()
  {
  	$params['limit'] = $limit = 3;
  	$params['offset'] = 2;
  	$params['order'] = array('annotation' => 'DESC');
  	$result = $this->object->fetch($params);

  	for($i = 3; $i >= 1; $i--)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  	}
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
  		$this->assertEqual($result[$key]['current_version'], $result[$key]['version']);
  		$version = $result[$key]['current_version'];
  		$this->assertEqual($result[$key]['annotation'], 'object_' . $key . '_annotation_version_' . $version);
  		$this->assertEqual($result[$key]['content'], 'object_' . $key . '_content_version_' . $version);
  		$this->assertEqual($result[$key]['class_name'], get_class($this->object));
  	}
  }

  function test_fetch_by_ids_no_params_wrong_ids()
  {
  	$ids_array = array(1, 2, 4, 5, 9, 12, 13, 15);
  	$result = $this->object->fetch_by_ids($ids_array);

  	$ids_array = array(1, 2, 4, 5);
  	$keys = array_keys($result);
  	sort($keys);
  	$this->assertEqual($keys, $ids_array);
  }

  function test_fetch_by_ids_no_class_restriction()
  {
  	$params = array('restrict_by_class' => false);
  	$ids_array = array(1, 2, 4, 5, 9, 12, 13, 15);
  	$result = $this->object->fetch_by_ids($ids_array, $params);

  	$ids_array = array(1, 2, 4, 5);
  	$keys = array_keys($result);
  	sort($keys);
  	$this->assertEqual($keys, $ids_array);
  }

  function test_fetch_by_ids_limit()
  {
  	$ids_array = array(1, 2, 3, 4, );
  	$params['limit'] = $limit = 3;

  	$result = $this->object->fetch_by_ids($ids_array, $params);

  	$this->assertEqual(sizeof($result), $limit);
  }

  function test_fetch_by_ids_limit_offset()
  {
  	$ids_array = array(1, 2, 3, 4, 5);
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $offset = 2;
  	$params['order'] = array('annotation' =>  'ASC');

  	$result = $this->object->fetch_by_ids($ids_array, $params);
  	$keys = array_keys($result);
  	sort($keys);
  	$this->assertEqual($keys, array(3, 4, 5));
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
  	$ids_array = array(1, 2, 3, 4, 6);
  	$params['limit'] = $limit = 2;
  	$params['offset'] = $offset = 2;
  	$params['order'] = array('annotation' =>  'DESC');

  	$result = $this->object->fetch_by_ids($ids_array, $params);

  	$this->assertEqual(array_keys($result), array(2, 1));
  }

  function test_fetch_by_ids_count_no_class_restriction()
  {
  	$ids_array = array(1, 2, 3, 4, 6, 7, 10);
  	$params = array('restrict_by_class' => false);
  	$result = $this->object->fetch_by_ids_count($ids_array, $params);

  	$this->assertEqual($result, 4);
  }
}

?>