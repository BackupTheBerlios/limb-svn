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
require_once(LIMB_DIR . '/tests/cases/db_test.class.php');
require_once(LIMB_DIR . 'core/request/response.class.php');
require_once(LIMB_DIR . 'core/request/request.class.php');
require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');
require_once(LIMB_DIR . 'core/model/access_policy.class.php');

Mock::generate('request');

class fetch_object_controller_test extends site_object_controller
{
	function fetch_object_controller_test()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
				),
		);
		
		parent :: site_object_controller();
	}
}


class fetching_object1_test_version extends site_object
{		
	function _define_class_properties()
	{
		return array(
			'db_table_name' => 'site_object',
			'can_be_parent' => true,
			'controller_class_name' => 'fetch_object_controller_test'
		);
	}
}

class fetching_object2_test_version extends site_object
{		
	function _define_class_properties()
	{
		return array(
			'db_table_name' => 'site_object',
			'controller_class_name' => 'fetch_object_controller_test'
		);
	}
}

class fetching_test extends db_test 
{ 
	var $fetcher = null;
	var $access_policy = null;
	var $articles_object = null;
	var $article_object = null;
	var $root_node_id = '';
	var $child_node_ids = array();
	
	var $objects = array();
	  
  function setUp()
  {
  	parent :: setUp();
  	
  	$this->fetcher =& new fetcher();
  	
  	$this->fetcher->set_jip_status(true);
  	
  	$user_id = 10;
  	
  	$this->_login_user($user_id, array(103 => 'visitors', 104 => 'admin'));
  	
  	$obj1 = site_object_factory :: create('fetching_object1_test_version');
  	$obj2 = site_object_factory :: create('fetching_object2_test_version');

  	$obj1->set_identifier('root');
  	$obj1->set_title('Root');
  	$obj1->create(true);
  	$access[$obj1->get_id()] = array($user_id => array('r' => 1, 'w' => 1));
  	$this->root_node_id = $obj1->get_node_id();
  	$this->_add_object($obj1);
  	
  	$obj1->set_parent_node_id($this->root_node_id);
  	$obj1->set_identifier('articles');
  	$obj1->set_title('Articles');  	
  	$obj1->create();
  	$access[$obj1->get_id()] = array($user_id => array('r' => 1, 'w' => 1));
  	$this->_add_object($obj1);
  	
  	$this->articles_object = $obj1;
  	
  	$obj2->set_parent_node_id($obj1->get_node_id());
  	$obj2->set_identifier('article1');
  	$obj2->set_title('Article1');
  	$obj2->create();
  	$access[$obj2->get_id()] = array($user_id => array('r' => 1, 'w' => 1));
  	$this->child_node_ids[] = $obj2->get_node_id();
  	$this->_add_object($obj2);

  	$this->article_object = $obj2;

  	$obj2->set_parent_node_id($obj1->get_node_id());
  	$obj2->set_identifier('article2');
  	$obj2->set_title('Article2');
  	$obj2->create();
  	$access[$obj2->get_id()] = array($user_id => array('r' => 1, 'w' => 1));
  	$this->child_node_ids[] = $obj2->get_node_id();
  	$this->_add_object($obj2);

  	$obj2->set_parent_node_id($obj1->get_node_id());
  	$obj2->set_identifier('article3');
  	$obj2->set_title('Article3');
  	$obj2->create();
  	$access[$obj2->get_id()] = array($user_id => array('r' => 0, 'w' => 0));
  	$this->child_node_ids[] = $obj2->get_node_id();
  	$this->_add_object($obj2);

  	$this->access_policy =& access_policy :: instance();
  	
  	$this->access_policy->save_user_object_access($access);
  	
	 	$actions = array(
  		$user_id => array(
  				'display' => 1,
  		),
   	);

		$this->access_policy->save_user_action_access($obj1->get_class_id(), $actions);
		$this->access_policy->save_user_action_access($obj2->get_class_id(), $actions);
  }
  
  function tearDown()
  {
  	parent :: tearDown();
  	
  	$user =& user :: instance();
  	$user->logout();  	
  	
 		$this->objects = array();
 		$this->child_node_ids = array();
 		
 		$this->db->sql_delete('sys_site_object_tree');
 		$this->db->sql_delete('sys_site_object');
 		$this->db->sql_delete('sys_object_access');
 		$this->db->sql_delete('sys_action_access');
 		
 		$this->fetcher->flush_cache();
  }
  
  function _login_user($id, $groups)
  {
  	$user =& user :: instance();
  	
  	$user->_set_id($id);
  	$user->_set_groups($groups);  	
  }   
  
  function _add_object($object)
  {
  	$this->objects[] = $object->export_attributes();
  }
		      
  function test_map_by_url()
  { 
  	$node =& $this->fetcher->map_url_to_node('/no/such/url');
  	$this->assertIdentical($node, false);

  	$node =& $this->fetcher->map_url_to_node('http://www.wow-baby.com/root/articles?id=2#wow');
  	
		$this->assertNotIdentical($node, false);
  	$this->assertEqual($node['identifier'], 'articles');  	
  }
  
  function test_map_current_request_phpself()
  { 	
  	$php_self = $_SERVER['PHP_SELF'];
  	$_SERVER['PHP_SELF'] = '/root/articles';
  	
  	$node =& $this->fetcher->map_request_to_node();
  	
		$this->assertNotIdentical($node, false);
  	$this->assertEqual($node['identifier'], 'articles');  	
  	
  	$_SERVER['PHP_SELF'] = $php_self;	
  }

  function test_map_current_request_node_id()
  { 	
  	$php_self = $_SERVER['PHP_SELF'];
  	$_SERVER['PHP_SELF'] = '/root/articles/no/such/article';
  	
  	$request = new Mockrequest($this);
  	$request->setReturnValue('get_attribute', $this->articles_object->get_node_id(), array('node_id'));
  	
  	$node =& $this->fetcher->map_request_to_node($request);

		$_SERVER['PHP_SELF'] = $php_self;
		
		$this->assertNotIdentical($node, false);
  	$this->assertEqual($node['identifier'], 'articles');  	
  	
  	$request->tally();
  }
  
  function test_fetch_one_by_node_id()
  {
  	$object_data =& $this->fetcher->fetch_one_by_node_id($this->articles_object->get_node_id());
  	$this->assertEqual($object_data['path'], '/root/articles');
		$this->_compare_data_with_object($object_data, $this->objects[1]);
		$this->_check_object_actions($object_data);
  }
  
  function test_fetch_one_by_path()
  {
  	$object_data = $this->fetcher->fetch_one_by_path('/root/articles');
		$this->_compare_data_with_object($object_data, $this->objects[1]);
		$this->_check_object_actions($object_data);
  }
  
  function _compare_data_with_object($data, $object_attribs)
  {
		foreach($object_attribs as $key => $value)
			$this->assertEqual($value, $data[$key], $key . ' = ' . $value . ' doesn\'t match');
  }
  
  function _check_object_actions($object_attribs)
  {
  	$arr = array($object_attribs);
  	$this->access_policy->assign_actions_to_objects($arr);
  	
  	$this->assertEqual($object_attribs['actions'], $arr[0]['actions']);
  }

  function test_fetch_no_such_path()
  {
  	$object_data = $this->fetcher->fetch_sub_branch('/no/such/path', 'fetching_object2_test_version', $counter);
  	$this->assertIdentical($object_data, array());
  }
  
  function test_fetch()
  {
  	$params = array(
  		'depth' => -1,
  	);	
  	
  	$arr = $this->fetcher->fetch_sub_branch('/root', 'fetching_object2_test_version', $counter, $params);
  	
  	$this->assertEqual($counter, 2);
  	$this->assertEqual(sizeof($arr), $counter);

  	$this->_compare_data_with_object($record = reset($arr), $this->objects[2]);
  	$this->assertEqual($record['path'], '/root/articles/article1');

  	$this->_compare_data_with_object($record = next($arr), $this->objects[3]);
  	$this->assertEqual($record['path'], '/root/articles/article2');
  }

  function test_fetch_limit()
  {
  	$params = array(
  		'depth' => -1,
  		'limit' => 1,
  	);	
  	$arr = $this->fetcher->fetch_sub_branch('/root', 'fetching_object2_test_version', $counter, $params);
  	
  	$this->assertEqual($counter, 2);
  	$this->assertEqual(sizeof($arr), $params['limit']);

  	$this->_compare_data_with_object($record = reset($arr), $this->objects[2]);
  	$this->assertEqual($record['path'], '/root/articles/article1');
  }
  
  function test_fetch_depth()
  {
  	$params = array(
  		'depth' => 1,
  	);	
  	$arr = $this->fetcher->fetch_sub_branch('/root', 'fetching_object2_test_version', $counter, $params);
  	
  	$this->assertEqual($counter, 0);
  	$this->assertEqual(sizeof($arr), 0);
  }

  function test_fetch_no_class_restriction()
  {
  	$params = array(
  		'depth' => 2,
  		'restrict_by_class' => 0
  	);	
  	
  	$arr = $this->fetcher->fetch_sub_branch('/root', 'site_object', $counter, $params);
  	
  	$this->assertEqual($counter, 3);
  	$this->assertEqual(sizeof($arr), 3);
  }
  
  function test_fetch_by_node_ids()
  {
  	$arr = $this->fetcher->fetch_by_node_ids($this->child_node_ids, 'fetching_object2_test_version', $counter);

  	$this->assertEqual($counter, 3);
  	$this->assertEqual(sizeof($arr), $counter);

  	$this->_compare_data_with_object($record = reset($arr), $this->objects[2]);
  	$this->assertEqual($record['path'], '/root/articles/article1');

  	$this->_compare_data_with_object($record = next($arr), $this->objects[3]);
  	$this->assertEqual($record['path'], '/root/articles/article2');

  	$this->_compare_data_with_object($record = next($arr), $this->objects[4]);
  	$this->assertEqual($record['path'], '/root/articles/article3');
  }
}

?>