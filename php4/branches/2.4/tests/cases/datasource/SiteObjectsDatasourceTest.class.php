<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/datasources/SiteObjectsDatasource.class.php');
require_once(LIMB_DIR . '/class/site_objects/SiteObjectController.class.php');
require_once(LIMB_DIR . '/class/permissions/Authorizer.interface.php');
require_once(LIMB_DIR . '/class/finders/DataFinder.interface.php');
require_once(LIMB_DIR . '/class/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/cache/CacheRegistry.class.php');

Mock :: generatePartial('SiteObjectsDatasource', 'SpecialSiteObjectsDatasource',
  array('_getBehavioursIds', 'getObjectIds', '_getFinder'));

Mock :: generatePartial('SiteObjectsDatasource',
                        'SpecialDatasourceForCacheHit',
                        array('getAccessibleObjectIds',
                              '_collectParams',
                              '_collectRawSqlParams', '_getFinder'));

Mock :: generate('Authorizer');
Mock :: generate('DataFinder');
Mock :: generate('SiteObjectController');
Mock :: generate('LimbToolkit');
Mock :: generate('CacheRegistry');

class SiteObjectsDatasourceTest extends LimbTestCase
{
  var $db;
  var $datasource;
  var $authorizer;
  var $site_object;
  var $cache;
  var $toolkit;

  function setUp()
  {
    $this->db =& LimbDbPool :: getConnection();
    $this->datasource = new SpecialSiteObjectsDatasource($this);

    $this->authorizer = new MockAuthorizer($this);
    $this->finder = new MockDataFinder($this);
    $this->datasource->setReturnReference('_getFinder', $this->finder);

    $this->cache = new MockCacheRegistry($this);

    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnReference('getAuthorizer', $this->authorizer);
    $this->toolkit->setReturnReference('getCache', $this->cache);

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

  function testFetch()
  {
    $objects = array(
      1 => array('id' => 1, 'node_id' => 10, 'parent_node_id' => 5, 'identifier' => 'test1'),
      2 => array('id' => 2, 'node_id' => 11, 'parent_node_id' => 5, 'identifier' => 'test2')
    );

    $behaviours = array('test_behaviour1', 'test_behaviour2');

    $this->authorizer->setReturnValue('getAccessibleObjectIds',
                                      $object_ids = array(1, 2),
                                      array($object_ids, $action = 'testAction'));

    $params = array('limit' => 10, 'offset' => 5, 'order' => null);
    $sql_params = array('conditions' => array('sso.class_id = 1',
                                              " AND sso.id IN ('1' , '2')",
                                              ' AND sys_class.name = some_class',
                                              " AND sso.behaviour_id IN ('100' , '101')"));

    $this->finder->expectOnce('find', array($params, $sql_params));
    $this->finder->setReturnValue('find', $objects, array($params, $sql_params));

    $this->datasource->expectOnce('_getBehavioursIds');
    $this->datasource->setReturnValue('_getBehavioursIds', array(100, 101));

    $this->datasource->setObjectIds($object_ids);
    $this->datasource->expectOnce('getObjectIds');
    $this->datasource->setReturnValue('getObjectIds', $object_ids);
    $this->datasource->setBehaviours($behaviours);
    $this->datasource->setLimit(10);
    $this->datasource->setOffset(5);
    $this->datasource->setRestrictionClassName('some_class');
    $this->datasource->setFindMethod('find');
    $this->datasource->setFinderName('some_finder');
    $this->datasource->setPermissionsAction($action);
    $this->datasource->setRawSqlParams(array('conditions' => array('sso.class_id = 1')));

    $fetched_objects = $this->datasource->fetch();

    $this->assertEqual(sizeof($fetched_objects), 2);
  }

  function testFetchCacheHit()
  {
    $datasource = new SpecialDatasourceForCacheHit($this);
    $datasource->setReturnReference('_getFinder', $this->finder);
    $datasource->setReturnValue('_collectParams', $params = array('p1' => '1'));
    $datasource->setReturnValue('_collectRawSqlParams', $sql_params = array('p2' => '2'));

    $datasource->setFindMethod($method = 'find');

    $this->finder->expectNever($method);

    $this->cache->expectOnce('get', array(array($params, $sql_params, $method),
                                          SITE_OBJECTS_CACHE_GROUP));
    $this->cache->setReturnValue('get', $objects = 'some data');

    $this->assertEqual($datasource->fetch(), $objects);
  }

  function testFetchCacheWrite()
  {
    $objects = array(
      1 => array('id' => 1, 'node_id' => 10, 'parent_node_id' => 5, 'identifier' => 'test1'),
      2 => array('id' => 2, 'node_id' => 11, 'parent_node_id' => 5, 'identifier' => 'test2')
    );

    $datasource = new SpecialDatasourceForCacheHit($this);
    $datasource->setReturnReference('_getFinder', $this->finder);
    $datasource->setReturnValue('_collectParams', $params = array('p1' => '1'));
    $datasource->setReturnValue('_collectRawSqlParams', $sql_params = array('p2' => '2'));

    $datasource->setFindMethod($method = 'find');

    $key = array($params, $sql_params, $method);
    $this->cache->expectOnce('get', array($key, SITE_OBJECTS_CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->finder->expectOnce('find', array($params, $sql_params));
    $this->finder->setReturnValue('find', $objects, array($params, $sql_params));

    $this->cache->expectOnce('put', array($key,$objects, SITE_OBJECTS_CACHE_GROUP));

    $this->assertEqual($datasource->fetch(), $objects);
  }

  function testCountTotalOk()
  {
    Mock :: generatePartial('SiteObjectsDatasource',
                            'SpecialDatasourceForCountTotalOk',
                            array('getAccessibleObjectIds', '_getFinder'));

    $datasource = new SpecialDatasourceForCountTotalOk($this);
    $datasource->SiteObjectsDatasource();
    $datasource->setReturnReference('_getFinder', $this->finder);
    $object_ids = array(1, 2);

    $datasource->setReturnValue('getAccessibleObjectIds', $object_ids);

    $sql_params = array('conditions' => array('sso.class_id = 1',
                                              " AND sso.id IN ('1' , '2')",
                                              ' AND sys_class.name = ' . $class_name = 'some_class'));

    $this->finder->expectOnce('findCount', array($sql_params));
    $this->finder->setReturnValue('findCount', 2, array($sql_params));

    $datasource->setObjectIds($object_ids);
    $datasource->setRestrictionClassName($class_name);
    $datasource->setFindMethod('find');
    $datasource->setRawSqlParams(array('conditions' => array('sso.class_id = 1')));

    $this->assertEqual(2, $datasource->countTotal());
  }

  function testCountTotalCacheHit()
  {
    $datasource = new SpecialDatasourceForCacheHit($this);
    $datasource->setReturnReference('_getFinder', $this->finder);
    $datasource->setReturnValue('_collectRawSqlParams', $sql_params = array('p2' => '2'));

    $datasource->setFindMethod('find');

    $this->finder->expectNever($method = 'findCount');

    $this->cache->expectOnce('get', array(array($sql_params, $method),
                                          SITE_OBJECTS_CACHE_GROUP));
    $this->cache->setReturnValue('get', $result = 101);

    $this->assertEqual($datasource->countTotal(), $result);
  }

  function testCountTotalCacheWrite()
  {
    $result = 101;

    $datasource = new SpecialDatasourceForCacheHit($this);
    $datasource->setReturnReference('_getFinder', $this->finder);
    $datasource->setReturnValue('_collectRawSqlParams', $sql_params = array('p2' => '2'));

    $datasource->setFindMethod('find');

    $key = array($sql_params, $method = 'findCount');
    $this->cache->expectOnce('get', array($key, SITE_OBJECTS_CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->finder->expectOnce($method, array($sql_params));
    $this->finder->setReturnValue($method, $result, array($sql_params));

    $this->cache->expectOnce('put', array($key, $result, SITE_OBJECTS_CACHE_GROUP));

    $this->assertEqual($datasource->countTotal(), $result);
  }

  function testGetAccessibleObjectIdsCacheHit()
  {
    $this->datasource->setReturnValue('getObjectIds', $ids = array(1,2,3));
    $this->datasource->setPermissionsAction('test-action');

    $this->cache->expectOnce('get', array(array($ids, 'test-action'),
                                          SITE_OBJECTS_CACHE_GROUP));
    $this->cache->setReturnValue('get', $result = array(1,2));

    $this->authorizer->expectNever('getAccessibleObjectIds');

    $this->assertEqual($this->datasource->getAccessibleObjectIds(), $result);
  }

  function testGetAccessibleObjectIdsCacheWrite()
  {
    $this->datasource->setReturnValue('getObjectIds', $ids = array(1,2,3));
    $this->datasource->setPermissionsAction('test-action');

    $this->cache->expectOnce('get', array(array($ids, 'test-action'),
                                          SITE_OBJECTS_CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->authorizer->expectOnce('getAccessibleObjectIds');
    $this->authorizer->setReturnValue('getAccessibleObjectIds', $result = array(1,2));

    $this->cache->setReturnValue('put', array(array($ids, 'test-action'),
                                              $result,
                                              SITE_OBJECTS_CACHE_GROUP));

    $this->assertEqual($this->datasource->getAccessibleObjectIds(), $result);
  }

  function testFlushCache()
  {
    $this->cache->expectOnce('flush', array(SITE_OBJECTS_CACHE_GROUP));
    $this->datasource->flushCache();
  }
}

?>