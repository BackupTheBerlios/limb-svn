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
require_once(LIMB_DIR . '/class/core/datasources/site_objects_datasource.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object_controller.class.php');
require_once(LIMB_DIR . '/class/core/permissions/authorizer.interface.php');
require_once(LIMB_DIR . '/class/core/finders/data_finder.interface.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/cache/cache_registry.class.php');
                      
Mock :: generatePartial('site_objects_datasource', 'special_site_objects_datasource',
  array('_get_behaviours_ids', 'get_object_ids', '_get_finder'));

Mock :: generatePartial('site_objects_datasource', 
                        'special_datasource_for_cache_hit',
                        array('get_accessible_object_ids', 
                              '_collect_params', 
                              '_collect_raw_sql_params', '_get_finder'));

Mock :: generate('authorizer');
Mock :: generate('data_finder');
Mock :: generate('site_object_controller');
Mock :: generate('LimbToolkit');
Mock :: generate('CacheRegistry');

class site_objects_datasource_test extends LimbTestCase
{
  var $db;
	var $datasource;
	var $authorizer;
	var $site_object;
  var $cache;
  var $toolkit;

  function setUp()
  {
    $this->db = db_factory :: instance();
  	$this->datasource = new special_site_objects_datasource($this);

  	$this->authorizer = new Mockauthorizer($this);
  	$this->finder = new Mockdata_finder($this);
    $this->datasource->setReturnValue('_get_finder', $this->finder);
    
    $this->cache = new MockCacheRegistry($this);

    $this->toolkit = new MockLimbToolkit($this);
    
    $this->toolkit->setReturnValue('getAuthorizer', $this->authorizer);
    $this->toolkit->setReturnValue('getCache', $this->cache);
    
    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->authorizer->tally();
  	$this->finder->tally();
    $this->cache->tally();
    
    $this->datasource->reset();
    
    Limb :: popToolkit();
  }
  
  function test_fetch()
  {
    $objects = array(
      1 => array('id' => 1, 'node_id' => 10, 'parent_node_id' => 5, 'identifier' => 'test1'),
      2 => array('id' => 2, 'node_id' => 11, 'parent_node_id' => 5, 'identifier' => 'test2')
    );
    
    $behaviours = array('test_behaviour1', 'test_behaviour2');

    $this->authorizer->setReturnValue('get_accessible_object_ids', 
                                      $object_ids = array(1, 2), 
                                      array($object_ids, $action = 'test_action'));

    $params = array('limit' => 10, 'offset' => 5, 'order' => null);
    $sql_params = array('conditions' => array('sso.class_id = 1',  
                                              " AND sso.id IN ('1' , '2')", 
                                              ' AND sys_class.name = some_class',
                                              " AND sso.behaviour_id IN ('100' , '101')"));

    $this->finder->expectOnce('find', array($params, $sql_params));
    $this->finder->setReturnValue('find', $objects, array($params, $sql_params));
    
    $this->datasource->expectOnce('_get_behaviours_ids');
    $this->datasource->setReturnValue('_get_behaviours_ids', array(100, 101));
    
    $this->datasource->set_object_ids($object_ids);
    $this->datasource->expectOnce('get_object_ids');
    $this->datasource->setReturnValue('get_object_ids', $object_ids);
    $this->datasource->set_behaviours($behaviours);
    $this->datasource->set_limit(10);
    $this->datasource->set_offset(5);
    $this->datasource->set_restriction_class_name('some_class');
    $this->datasource->set_find_method('find');
    $this->datasource->set_finder_name('some_finder');
    $this->datasource->set_permissions_action($action);
    $this->datasource->set_raw_sql_params(array('conditions' => array('sso.class_id = 1')));
    
    $fetched_objects = $this->datasource->fetch();

    $this->assertEqual(sizeof($fetched_objects), 2);
  }

  function test_fetch_cache_hit()
  {
    $datasource = new special_datasource_for_cache_hit($this);
    $datasource->setReturnValue('_get_finder', $this->finder);
    $datasource->setReturnValue('_collect_params', $params = array('p1' => '1'));
    $datasource->setReturnValue('_collect_raw_sql_params', $sql_params = array('p2' => '2'));
    
    $datasource->set_find_method($method = 'find');

    $this->finder->expectNever($method);
    
    $this->cache->expectOnce('get', array(array($params, $sql_params, $method), 
                                          site_objects_datasource :: CACHE_GROUP));
    $this->cache->setReturnValue('get', $objects = 'some_data');
    
    $this->assertEqual($datasource->fetch(), $objects);
  }

  function test_fetch_cache_write()
  {
    $objects = array(
      1 => array('id' => 1, 'node_id' => 10, 'parent_node_id' => 5, 'identifier' => 'test1'),
      2 => array('id' => 2, 'node_id' => 11, 'parent_node_id' => 5, 'identifier' => 'test2')
    );
    
    $datasource = new special_datasource_for_cache_hit($this);
    $datasource->setReturnValue('_get_finder', $this->finder);
    $datasource->setReturnValue('_collect_params', $params = array('p1' => '1'));
    $datasource->setReturnValue('_collect_raw_sql_params', $sql_params = array('p2' => '2'));
    
    $datasource->set_find_method($method = 'find');

    $key = array($params, $sql_params, $method);
    $this->cache->expectOnce('get', array($key, site_objects_datasource :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->finder->expectOnce('find', array($params, $sql_params));
    $this->finder->setReturnValue('find', $objects, array($params, $sql_params));

    $this->cache->expectOnce('put', array($key,$objects, site_objects_datasource :: CACHE_GROUP));
    
    $this->assertEqual($datasource->fetch(), $objects);
  }
    
  function test_count_total_ok()
  {
    Mock :: generatePartial('site_objects_datasource', 
                            'special_datasource_for_count_total_ok',
                            array('get_accessible_object_ids', '_get_finder'));

    $datasource = new special_datasource_for_count_total_ok($this);
    $datasource->__construct();
    $datasource->setReturnValue('_get_finder', $this->finder);
    $object_ids = array(1, 2); 

    $datasource->setReturnValue('get_accessible_object_ids', $object_ids);
    
    $sql_params = array('conditions' => array('sso.class_id = 1',
                                              " AND sso.id IN ('1' , '2')",
                                              ' AND sys_class.name = ' . $class_name = 'some_class'));
    
    $this->finder->expectOnce('find_count', array($sql_params));
    $this->finder->setReturnValue('find_count', 2, array($sql_params));

    $datasource->set_object_ids($object_ids);
    $datasource->set_restriction_class_name($class_name);
    $datasource->set_find_method('find');
    $datasource->set_raw_sql_params(array('conditions' => array('sso.class_id = 1')));

    $this->assertEqual(2, $datasource->count_total());
  }
  
  function test_count_total_cache_hit()
  {
    $datasource = new special_datasource_for_cache_hit($this);
    $datasource->setReturnValue('_get_finder', $this->finder);
    $datasource->setReturnValue('_collect_raw_sql_params', $sql_params = array('p2' => '2'));
    
    $datasource->set_find_method('find');

    $this->finder->expectNever($method = 'find_count');
    
    $this->cache->expectOnce('get', array(array($sql_params, $method), 
                                          site_objects_datasource :: CACHE_GROUP));
    $this->cache->setReturnValue('get', $result = 101);
    
    $this->assertEqual($datasource->count_total(), $result);
  }
  
  function test_count_total_cache_write()
  {
    $result = 101;
    
    $datasource = new special_datasource_for_cache_hit($this);
    $datasource->setReturnValue('_get_finder', $this->finder);
    $datasource->setReturnValue('_collect_raw_sql_params', $sql_params = array('p2' => '2'));
    
    $datasource->set_find_method('find');

    $key = array($sql_params, $method = 'find_count');
    $this->cache->expectOnce('get', array($key, site_objects_datasource :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->finder->expectOnce($method, array($sql_params));
    $this->finder->setReturnValue($method, $result, array($sql_params));

    $this->cache->expectOnce('put', array($key, $result, site_objects_datasource :: CACHE_GROUP));
    
    $this->assertEqual($datasource->count_total(), $result);
  }  
  
  function test_get_accessible_object_ids_cache_hit()
  { 
    $this->datasource->setReturnValue('get_object_ids', $ids = array(1,2,3));
    $this->datasource->set_permissions_action('test-action');
    
    $this->cache->expectOnce('get', array(array($ids, 'test-action'), 
                                          site_objects_datasource :: CACHE_GROUP));
    $this->cache->setReturnValue('get', $result = array(1,2));
    
    $this->authorizer->expectNever('get_accessible_object_ids');

    $this->assertEqual($this->datasource->get_accessible_object_ids(), $result); 
  }
  
  function test_get_accessible_object_ids_cache_write()
  { 
    $this->datasource->setReturnValue('get_object_ids', $ids = array(1,2,3));
    $this->datasource->set_permissions_action('test-action');
    
    $this->cache->expectOnce('get', array(array($ids, 'test-action'), 
                                          site_objects_datasource :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);
    
    $this->authorizer->expectOnce('get_accessible_object_ids');
    $this->authorizer->setReturnValue('get_accessible_object_ids', $result = array(1,2));
    
    $this->cache->setReturnValue('put', array(array($ids, 'test-action'),
                                              $result,    
                                              site_objects_datasource :: CACHE_GROUP));

    $this->assertEqual($this->datasource->get_accessible_object_ids(), $result); 
  }
  
  function test_flush_cache()
  {
    $this->cache->expectOnce('flush', array(site_objects_datasource :: CACHE_GROUP));
    $this->datasource->flush_cache();
  }
}

?>