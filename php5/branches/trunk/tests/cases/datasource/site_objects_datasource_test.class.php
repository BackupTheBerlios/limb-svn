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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . '/class/core/permissions/authorizer.interface.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/cache/cache_registry.class.php');
                      
Mock :: generatePartial('site_objects_datasource', 'special_site_objects_datasource',
  array('_get_behaviours_ids', 'get_object_ids'));

Mock :: generatePartial('site_objects_datasource', 
                        'special_datasource_for_cache_hit',
                        array('get_accessible_object_ids', 
                              '_collect_params', 
                              '_collect_raw_sql_params'));

Mock :: generate('authorizer');
Mock :: generate('site_object');
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
  	$this->site_object = new Mocksite_object($this);
    $this->cache = new MockCacheRegistry($this);

    $this->toolkit = new MockLimbToolkit($this);
    
    $this->toolkit->setReturnValue('getAuthorizer', $this->authorizer);
    $this->toolkit->setReturnValue('createSiteObject', $this->site_object);
    $this->toolkit->setReturnValue('getCache', $this->cache);
    
    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->authorizer->tally();
  	$this->site_object->tally();
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
                                              ' AND sso.class_id = 10',
                                              " AND sso.behaviour_id IN ('100' , '101')"));

    $this->site_object->expectOnce('get_class_id');
    $this->site_object->setReturnValue('get_class_id', 10);
    $this->site_object->expectOnce('fetch', array($params, $sql_params));
    $this->site_object->setReturnValue('fetch', $objects, array($params, $sql_params));
    
    $this->datasource->expectOnce('_get_behaviours_ids');
    $this->datasource->setReturnValue('_get_behaviours_ids', array(100, 101));
    
    $this->datasource->set_object_ids($object_ids);
    $this->datasource->expectOnce('get_object_ids');
    $this->datasource->setReturnValue('get_object_ids', $object_ids);
    $this->datasource->set_behaviours($behaviours);
    $this->datasource->set_limit(10);
    $this->datasource->set_offset(5);
    $this->datasource->set_restrict_by_class(true);
    $this->datasource->set_fetch_method('fetch');
    $this->datasource->set_site_object_class_name('some_class');
    $this->datasource->set_permissions_action($action);
    $this->datasource->set_raw_sql_params(array('conditions' => array('sso.class_id = 1')));

    $fetched_objects = $this->datasource->fetch();

    $this->assertEqual(sizeof($fetched_objects), 2);
  }

  function test_fetch_cache_hit()
  {
    $datasource = new special_datasource_for_cache_hit($this);
    $datasource->setReturnValue('_collect_params', $params = array('p1' => '1'));
    $datasource->setReturnValue('_collect_raw_sql_params', $sql_params = array('p2' => '2'));
    
    $datasource->set_fetch_method($method = 'fetch');

    $this->site_object->expectNever($method);
    
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
    $datasource->setReturnValue('_collect_params', $params = array('p1' => '1'));
    $datasource->setReturnValue('_collect_raw_sql_params', $sql_params = array('p2' => '2'));
    
    $datasource->set_fetch_method($method = 'fetch');

    $key = array($params, $sql_params, $method);
    $this->cache->expectOnce('get', array($key, site_objects_datasource :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->site_object->expectOnce('fetch', array($params, $sql_params));
    $this->site_object->setReturnValue('fetch', $objects, array($params, $sql_params));

    $this->cache->expectOnce('put', array($key,$objects, site_objects_datasource :: CACHE_GROUP));
    
    $this->assertEqual($datasource->fetch(), $objects);
  }
    
  function test_count_total_ok()
  {
    Mock :: generatePartial('site_objects_datasource', 
                            'special_datasource_for_count_total_ok',
                            array('get_accessible_object_ids'));

    $datasource = new special_datasource_for_count_total_ok($this);
    $datasource->__construct();
    $object_ids = array(1, 2); 

    $datasource->setReturnValue('get_accessible_object_ids', $object_ids);
    
    $sql_params = array('conditions' => array('sso.class_id = 1',
                                              " AND sso.id IN ('1' , '2')",
                                              ' AND sso.class_id = 10'));
    
    $this->site_object->expectOnce('get_class_id');
    $this->site_object->setReturnValue('get_class_id', 10);
    $this->site_object->expectOnce('fetch_count', array($sql_params));
    $this->site_object->setReturnValue('fetch_count', 2, array($sql_params));

    $datasource->set_object_ids($object_ids);
    $datasource->set_restrict_by_class(true);
    $datasource->set_fetch_method('fetch');
    $datasource->set_site_object_class_name('some_class');
    $datasource->set_raw_sql_params(array('conditions' => array('sso.class_id = 1')));

    $this->assertEqual(2, $datasource->count_total());
  }
  
  function test_count_total_cache_hit()
  {
    $datasource = new special_datasource_for_cache_hit($this);
    $datasource->setReturnValue('_collect_raw_sql_params', $sql_params = array('p2' => '2'));
    
    $datasource->set_fetch_method('fetch');

    $this->site_object->expectNever($method = 'fetch_count');
    
    $this->cache->expectOnce('get', array(array($sql_params, $method), 
                                          site_objects_datasource :: CACHE_GROUP));
    $this->cache->setReturnValue('get', $result = 101);
    
    $this->assertEqual($datasource->count_total(), $result);
  }
  
  function test_count_total_cache_write()
  {
    $result = 101;
    
    $datasource = new special_datasource_for_cache_hit($this);
    $datasource->setReturnValue('_collect_raw_sql_params', $sql_params = array('p2' => '2'));
    
    $datasource->set_fetch_method('fetch');

    $key = array($sql_params, $method = 'fetch_count');
    $this->cache->expectOnce('get', array($key, site_objects_datasource :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->site_object->expectOnce($method, array($sql_params));
    $this->site_object->setReturnValue($method, $result, array($sql_params));

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