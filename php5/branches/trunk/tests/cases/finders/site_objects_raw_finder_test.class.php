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
require_once(LIMB_DIR . '/class/db_tables/db_table_factory.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_module.class.php');
require_once(LIMB_DIR . '/class/core/finders/site_objects_raw_finder.class.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
require_once(LIMB_DIR . '/class/core/tree/materialized_path_tree.class.php');

Mock :: generate('db_module');
Mock :: generate('LimbToolkit');

Mock :: generatePartial('site_objects_raw_finder',
                        'site_objects_raw_finder_find_version_mock',
                        array());

class site_objects_raw_finder_find_test_version extends site_objects_raw_finder_find_version_mock
{
  var $_mocked_methods = array('find');

  public function find($params = array(), $sql_params = array()) 
  { 
    $args = func_get_args();
    return $this->_mock->_invoke('find', $args); 
  }
}

class site_objects_raw_finder_test extends LimbTestCase
{
	var $class_id;
	var $finder;
	var $db;
	var $root_node_id;
  var	$behaviour_id;

  function setUp()
  {    
    $this->db = db_factory :: instance();

    $this->_clean_up();

  	$this->_insert_sys_class_record();
  	$this->_insert_sys_behaviour_record();
    
  	$this->_insert_sys_site_object_records();
  	$this->_insert_fake_sys_site_object_records();
    
    $this->finder = new site_objects_raw_finder();
  }

  function tearDown()
  {
    $this->_clean_up();    
  }

  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_class');
  	$this->db->sql_delete('sys_behaviour');
  }
  
  function test_find_required_fields()
  {
    $sql_params['conditions'][] = ' AND sso.id = 1';
    $objects_data = $this->finder->find(array(), $sql_params);
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
    $this->assertEqual($record['parent_node_id'], $this->root_node_id);
    $this->assertTrue(array_key_exists('level', $record));
    $this->assertTrue(array_key_exists('priority', $record));
    $this->assertTrue(array_key_exists('children', $record));
    $this->assertEqual($record['class_id'], $this->class_id);
    $this->assertEqual($record['class_name'], 'site_object');
    $this->assertTrue(array_key_exists('behaviour_id', $record));
    $this->assertEqual($record['behaviour'], 'site_object_behaviour');
    $this->assertTrue(array_key_exists('icon', $record));
    $this->assertTrue(array_key_exists('sort_order', $record));
    $this->assertTrue(array_key_exists('can_be_parent', $record));
  }
  
  function test_find_sql()
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
    
    $this->finder->find($params, $sql_params);
    
    Limb :: popToolkit();
    
    $db_mock->tally();
  }
  
	function test_find_count_sql_with_group()
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
    
    $this->assertEqual($result, $this->finder->find_count($sql_params));
    
    Limb :: popToolkit();
    
    $db_mock->tally();    
  }  

  function test_find_count_sql_no_group()
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

    $this->assertEqual(10, $this->finder->find_count($sql_params));
    
    Limb :: popToolkit();
    
    $db_mock->tally();    
  }

  function test_find_by_id()
  {
    $finder = new site_objects_raw_finder_find_test_version($this);
    $finder->expectOnce('find', array(array(), 
                                      array('conditions' => array(' AND sso.id='. $id = 100))));
    
    $finder->expectOnce('find');
    $finder->find_by_id($id);
    
    $finder->tally();
  }

  function test_find_no_params()
  {
  	$result = $this->finder->find();

  	for($i = 1; $i <=5; $i++)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  	}
  }

  function test_find_limit_offset()
  {
  	$params['limit'] = $limit = 3;
  	$params['offset'] = $limit = 2;
  	$result = $this->finder->find($params);

  	for($i = 3; $i <= 5; $i++)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  	}
  }

  function test_find_limit_offset_order()
  {
  	$params['limit'] = $limit = 3;
  	$params['offset'] = 2;
  	$params['order'] = array('title' => 'DESC'); 
  	$result = $this->finder->find($params);
 
  	for($i = 7; $i >=5; $i--)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  	}
  }
  
	function test_count()
	{
  	$result = $this->finder->find_count();
  	$this->assertEqual($result, 10);
	}
  
  function _insert_sys_class_record()
  {
  	$db_table = db_table_factory :: create('sys_class');
    $db_table->insert(array('name' => 'site_object'));
    
    $this->class_id = $db_table->get_last_insert_id(); 
  }

  function _insert_sys_behaviour_record()
  {
  	$db_table = db_table_factory :: create('sys_behaviour');
    $db_table->insert(array('name' => 'site_object_behaviour'));
    
    $this->behaviour_id = $db_table->get_last_insert_id(); 
  }

  function _insert_sys_site_object_records()
  {
    $tree = new materialized_path_tree();

		$values['identifier'] = 'root';
		$this->root_node_id = $tree->create_root_node($values, false, true);

  	$data = array();
  	for($i = 1; $i <= 5; $i++)
  	{
  		$version = mt_rand(1, 3);

  		$this->db->sql_insert('sys_site_object',
  			array(
  				'id' => $i,
  				'class_id' => $this->class_id,
  				'behaviour_id' => $this->behaviour_id,
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

  function _insert_fake_sys_site_object_records()
  {
  	$class_db_table = db_table_factory :: create('sys_class');
  	$class_db_table->insert(array('id' => 1001, 'class_name' => 'fake_class'));

    $tree = new materialized_path_tree();

  	$db_table =& db_table_factory :: create('sys_site_object');

  	$data = array();
  	for($i = 6; $i <= 10 ; $i++)
  	{
  		$this->db->sql_insert('sys_site_object',
  			array(
  				'id' => $i,
  				'class_id' => 1001,
  				'behaviour_id' => $this->behaviour_id,
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
}

?>