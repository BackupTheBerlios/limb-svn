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
require_once(LIMB_DIR . '/class/lib/db/db_module.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');

Mock :: generate('db_module');
Mock :: generate('LimbToolkit');

Mock :: generatePartial('site_object',
                        'site_object_fetch_test_version',
                        array('fetch'));

class site_object_fetch_test extends LimbTestCase
{
	var $class_id;
	var $object;
	var $db;

  function setUp()
  {    
    $this->db = db_factory :: instance();

    $this->_clean_up();

    $this->_init_object();
    $this->_init_fetch_data($this->object);

    $this->class_id = $this->object->get_class_id();
  }

  function tearDown()
  {
    $this->_clean_up();    
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

  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_class');
  	$this->db->sql_delete('sys_behaviour');
  }
  
  function test_fetch_required_fields()
  {
    $sql_params['conditions'][] = ' AND sso.id = 1';
    $objects_data = $this->object->fetch(array(), $sql_params);
    $record = reset($objects_data);
    
    $this->assertEqual($record['identifier'], 'object_1');
    $this->assertEqual($record['title'], 'object_1_title');
    $this->assertTrue(array_key_exists('current_version', $record));
    $this->assertTrue(array_key_exists('modified_date', $record));
    $this->assertEqual($record['status'], 0);
    $this->assertTrue(array_key_exists('created_date', $record));
    $this->assertTrue(array_key_exists('creator_id', $record));
    $this->assertEqual($record['locale_id'], 'en');
    $this->assertTrue(array_key_exists('id', $record));
    $this->assertTrue(array_key_exists('node_id', $record));
    $this->assertTrue(array_key_exists('parent_node_id', $record));
    $this->assertTrue(array_key_exists('level', $record));
    $this->assertTrue(array_key_exists('priority', $record));
    $this->assertTrue(array_key_exists('children', $record));
    $this->assertEqual($record['class_id'], $this->class_id);
    $this->assertEqual($record['class_name'], get_class($this->object));
    $this->assertTrue(array_key_exists('behaviour_id', $record));
    $this->assertEqual($record['behaviour'], get_class($this->object) . '_behaviour');
    $this->assertTrue(array_key_exists('icon', $record));
    $this->assertTrue(array_key_exists('sort_order', $record));
    $this->assertTrue(array_key_exists('can_be_parent', $record));
  }
  
  function test_fetch_sql()
  {
    $db_mock = new Mockdb_module($this);
    $toolkit = new MockLimbToolkit($this);
    
    $toolkit->setReturnValue('getDB', $db_mock);
        
    Limb :: registerToolkit($toolkit);
    
    $params = array();
    $sql_params = array();
    
    $params['order'] = array('col1' => 'DESC', 'col2' => 'ASC');
    $params['limit'] = 10;
    $params['offset'] = 5;
    
    $sql_params['columns'][] = 'test-column1,';
    $sql_params['columns'][] = 'test-column2,';
    
    $sql_params['tables'][] = ',test-table1';
    $sql_params['tables'][] = ',test-table2';
    
    $sql_params['conditions'][] = 'OR test-condition1';
    $sql_params['conditions'][] = 'AND test-condition2';
    
    $sql_params['group'][] = 'GROUP BY test-group';
    
    $expectation = new WantedPatternExpectation(
    "~^SELECT.*sso\.locale_id as locale_id,.*test-column1, test-column2,.*sso\.title as title,.* sys_site_object_tree as ssot.*,test-table1 ,test-table2.*WHERE sys_class\.id = sso\.class_id.*AND ssot\.object_id = sso\.id.*OR test-condition1 AND test-condition2 GROUP BY test-group ORDER BY col1 DESC, col2 ASC$~s");
    
    $db_mock->expectOnce('sql_exec', array($expectation, 10, 5));
    $db_mock->expectOnce('get_array', array('id'));
    
    $this->object->fetch($params, $sql_params);
    
    Limb :: popToolkit();
    
    $db_mock->tally();
  }
  
	function test_fetch_count_sql_with_group()
  {
    $db_mock = new Mockdb_module($this);
    $toolkit = new MockLimbToolkit($this);
    
    $toolkit->setReturnValue('getDB', $db_mock);
        
    Limb :: registerToolkit($toolkit);
    
    $sql_params['tables'][] = ',table1';
    $sql_params['tables'][] = ',table2';
    
    $sql_params['conditions'][] = 'OR cond1';
    $sql_params['conditions'][] = 'AND cond2';
    
    $sql_params['group'][] = 'GROUP BY test-group';    

    $expectation = new WantedPatternExpectation(
    "~^SELECT COUNT\(sso\.id\) as count.*FROM sys_site_object as sso ,table1 ,table2.*WHERE sso\.id OR cond1 AND cond2 GROUP BY test-group~s");
    
    $db_mock->expectOnce('sql_exec', array($expectation));
    $db_mock->expectOnce('count_selected_rows');
    $db_mock->setReturnValue('count_selected_rows', $result = 10);
    
    $this->assertEqual($result, $this->object->fetch_count($sql_params));
    
    Limb :: popToolkit();
    
    $db_mock->tally();    
  }  

  function test_fetch_count_sql_no_group()
  {
    $db_mock = new Mockdb_module($this);
    $toolkit = new MockLimbToolkit($this);
    
    $toolkit->setReturnValue('getDB', $db_mock);
        
    Limb :: registerToolkit($toolkit);
    
    $sql_params['tables'][] = ',table1';
    $sql_params['tables'][] = ',table2';
    
    $sql_params['conditions'][] = 'OR cond1';
    $sql_params['conditions'][] = 'AND cond2';

    $expectation = new WantedPatternExpectation(
    "~^SELECT COUNT\(sso\.id\) as count.*FROM sys_site_object as sso ,table1 ,table2.*WHERE sso\.id OR cond1 AND cond2~s");
    
    $db_mock->expectOnce('sql_exec', array($expectation));
    $db_mock->expectNever('count_selected_rows');
    $db_mock->expectOnce('fetch_row');
    $db_mock->setReturnValue('fetch_row', array('count' => 10));
    
    $this->assertEqual(10, $this->object->fetch_count($sql_params));
    
    Limb :: popToolkit();
    
    $db_mock->tally();    
  }  

  function test_fetch_no_params()
  {
  	$result = $this->object->fetch();

  	for($i = 1; $i <=5; $i++)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
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
  	}
  }

  function test_fetch_limit_offset_order()
  {
  	$params['limit'] = $limit = 3;
  	$params['offset'] = 2;
  	$params['order'] = array('title' => 'DESC'); 
  	$result = $this->object->fetch($params);
 
  	for($i = 7; $i >=5; $i--)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  	}
  }
  
	function test_fetch_count()
	{
  	$result = $this->object->fetch_count();
  	$this->assertEqual($result, 10);
	}
}

?>