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
require_once(LIMB_DIR . 'class/core/request/request.class.php');
require_once(LIMB_DIR . 'class/lib/http/uri.class.php');
require_once(LIMB_DIR . 'class/core/fetcher.class.php');
require_once(LIMB_DIR . 'class/core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'class/core/permissions/authorizer.interface.php');
require_once(LIMB_DIR . 'class/core/tree/tree.class.php');
require_once(LIMB_DIR . 'class/core/site_objects/site_object.class.php');

Mock::generatePartial('fetcher', 'special_fetcher',
  array('_get_authorizer', '_get_site_object', '_get_tree'));

Mock::generatePartial('fetcher', 'special_fetcher2',
  array('fetch_by_ids', '_get_tree', '_get_object_class_name_by_id'));

Mock::generatePartial('fetcher', 'special_fetcher3',
  array('fetch_one_by_id', 'map_request_to_node', 'set_jip_status', 'fetch_by_ids', '_get_tree'));

Mock::generate('request');
Mock::generate('uri');
Mock::generate('authorizer');
Mock::generate('tree');
Mock::generate('site_object');
Mock::generate('site_object_controller');

class fetching_test extends LimbTestCase
{
	var $fetcher = null;

  var $request = null;
	var $authorizer = null;
	var $tree = null;
	var $site_object = null;

  function setUp()
  {
  	$this->fetcher = new special_fetcher($this);

    $this->request = new Mockrequest($this);
    $this->uri = new Mockuri($this);
  	$this->authorizer = new Mockauthorizer($this);
  	$this->tree = new Mocktree($this);
  	$this->site_object = new Mocksite_object($this);

  	$this->fetcher->setReturnValue('_get_authorizer', $this->authorizer);
  	$this->fetcher->setReturnValue('_get_site_object', $this->site_object);
  	$this->fetcher->setReturnValue('_get_tree', $this->tree);

  	$this->request->setReturnValue('get_uri', $this->uri);
  }

  function tearDown()
  {
    $this->fetcher->tally();
 		$this->fetcher->flush_cache();

    $this->request->tally();
    $this->uri->tally();
    $this->authorizer->tally();
  	$this->tree->tally();
  	$this->site_object->tally();
  }

  function test_map_uri_to_node_by_node_id()
  {
    $this->uri->setReturnValue('get_query_item', $node_id = 10, array('node_id'));

    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', $node = array('node_id' => $node_id), array($node_id));

    $this->assertEqual($node, $this->fetcher->map_uri_to_node($this->uri));
  }

  function test_map_uri_to_node_by_path()
  {
    $this->uri->setReturnValue('get_query_item', false, array('node_id'));
    $this->uri->setReturnValue('get_path', $path = '/path');

    $this->tree->expectOnce('get_node_by_path');
    $this->tree->setReturnValue('get_node_by_path', $node = array('node_id' => 10), array($path, '/', false));

    $this->assertEqual($node, $this->fetcher->map_uri_to_node($this->uri));
  }

  function test_map_request_to_node_by_node_id()
  {
    $this->request->setReturnValue('get', $node_id = 10, array('node_id'));
    $this->tree->setReturnValue('get_node', $node = array('node_id' => $node_id), array($node_id));

    $this->assertEqual($node, $this->fetcher->map_request_to_node($this->request));
  }

  function test_map_request_to_node_by_path()
  {
    $this->uri->setReturnValue('get_query_item', false, array('node_id'));
    $this->uri->setReturnValue('get_path', $path = '/path');

    $this->request->setReturnValue('get', false, array('node_id'));

    $this->tree->expectOnce('get_node_by_path');
    $this->tree->setReturnValue('get_node_by_path', $node = array('node_id' => 10), array($path, '/', false));

    $this->assertEqual($node, $this->fetcher->map_request_to_node($this->request));
  }

  function test_map_request_to_node_cache()
  {
    $this->request->expectOnce('get');

    $this->request->setReturnValue('get', $node_id = 10, array('node_id'));
    $this->tree->setReturnValue('get_node', $node = array('node_id' => $node_id), array($node_id));

    $this->fetcher->map_request_to_node($this->request);

    $this->assertEqual($node, $this->fetcher->map_request_to_node($this->request));
  }

  function test_fetch_by_ids()
  {
    $objects = array(
      1 => array('id' => 1, 'node_id' => 10, 'parent_node_id' => 5, 'identifier' => 'test1'),
      2 => array('id' => 2, 'node_id' => 11, 'parent_node_id' => 5, 'identifier' => 'test2')
    );

    $parents = array(
      5 => array('id' => 1, 'identifier' => 'root'),
    );

    $this->authorizer->setReturnValue('get_accessible_object_ids', $object_ids = array(1, 2), array($object_ids, '', $class_id = 10));
    $this->authorizer->ExpectOnce('assign_actions_to_objects', array($objects));

    $this->site_object->setReturnValue('get_class_id', $class_id);

    $params = array('limit' => 10, 'offset' => 5);

    $this->site_object->setReturnValue('fetch_by_ids', $objects, array($object_ids, $params));
    $this->site_object->setReturnValue('fetch_by_ids_count', 2, array($object_ids, $params));

    $this->tree->setReturnValue('get_parents', $parents, array(10));
    $this->tree->setReturnValue('get_parents', $parents, array(11));

    $counter = 0;

    $fetched_objects = $this->fetcher->fetch_by_ids($object_ids, 'some_class', $counter, $params, 'fetch_by_ids');

    $this->assertEqual($counter, 2);
    $this->assertEqual(sizeof($fetched_objects), 2);
    $this->assertEqual($fetched_objects[1]['path'], '/root/test1');
    $this->assertEqual($fetched_objects[2]['path'], '/root/test2');
  }

  function test_fetch_by_ids_no_count_method_call()
  {
    $objects = array(
      1 => array('id' => 1, 'node_id' => 10, 'parent_node_id' => 5, 'identifier' => 'test1'),
    );

    $parents = array(
      5 => array('id' => 1, 'identifier' => 'root'),
    );

    $this->authorizer->setReturnValue('get_accessible_object_ids', $object_ids = array(1, 2), array($object_ids, '', $class_id = 10));

    $this->site_object->setReturnValue('get_class_id', $class_id);

    $this->site_object->setReturnValue('fetch_by_ids', $objects, array($object_ids, array()));
    $this->site_object->expectNever('fetch_by_ids_count');

    $this->tree->setReturnValue('get_parents', $parents, array(10));

    $counter = null;

    $this->fetcher->fetch_by_ids($object_ids, 'some_class', $counter, array(), 'fetch_by_ids');
  }

  function test_fetch_by_ids_no_accessible_objects()
  {
    $this->authorizer->setReturnValue('get_accessible_object_ids', array(), array($object_ids = array(1, 2), '', $class_id = 10));

    $this->site_object->setReturnValue('get_class_id', $class_id);

    $this->assertEqual(array(), $this->fetcher->fetch_by_ids($object_ids, 'some_class', $counter, array(), 'fetch_by_ids'));
  }

  function test_fetch_by_node_ids_no_class_restriction()
  {
    $params['restrict_by_class'] = false;

    $this->authorizer->setReturnValue('get_accessible_object_ids', array(), array($object_ids = array(1, 2), '', null));
    $this->authorizer->expectOnce('get_accessible_object_ids',  array($object_ids, '', null));

    $this->site_object->expectNever('get_class_id');

    $this->assertEqual(array(), $this->fetcher->fetch_by_ids($object_ids, 'some_class', $counter, $params, 'fetch_by_ids'));
  }

  function test_fetch_by_node_ids_no_class_restriction2()
  {
    $this->authorizer->setReturnValue('get_accessible_object_ids', array(), array($object_ids = array(1, 2), '', null));
    $this->authorizer->expectOnce('get_accessible_object_ids',  array($object_ids, '', null));

    $this->site_object->expectNever('get_class_id');

    $this->assertEqual(array(), $this->fetcher->fetch_by_ids($object_ids, 'site_object', $counter, array(), 'fetch_by_ids'));
  }

  function test_fetch_by_node_ids_no_nodes()
  {
  	$fetcher = new special_fetcher2($this);
  	$fetcher->setReturnValue('_get_tree', $this->tree);
  	$fetcher->expectNever('fetch_by_ids');

  	$this->tree->setReturnValue('get_nodes_by_ids', array(), array($nodes_ids = array(1,2,3)));

  	$this->assertEqual(array(), $fetcher->fetch_by_node_ids($nodes_ids, '', $counter));

  	$fetcher->tally();
  }

  function test_fetch_by_node_ids()
  {
  	$fetcher = new special_fetcher2($this);
  	$fetcher->setReturnValue('_get_tree', $this->tree);

    $nodes = array(array('object_id' => 10), array('object_id' => 20));
  	$this->tree->setReturnValue('get_nodes_by_ids', $nodes, array($nodes_ids = array(1, 2)));

    $objects = array(
      20 => array('id' => 20, 'node_id' => 2),
      10 => array('id' => 10, 'node_id' => 1),
    );

    $counter = 0;
  	$fetcher->setReturnValue('fetch_by_ids', $objects, array(array(10, 20), 'some_loader', $counter, array(), 'fetch_by_ids'));
  	$this->assertIdentical($objects, $fetcher->fetch_by_node_ids($nodes_ids, 'some_loader', $counter));

  	$fetcher->tally();
  }

  function test_fetch_by_node_ids_as_keys()
  {
  	$fetcher = new special_fetcher2($this);
  	$fetcher->setReturnValue('_get_tree', $this->tree);

    $nodes = array(array('object_id' => 10), array('object_id' => 20));
  	$this->tree->setReturnValue('get_nodes_by_ids', $nodes, array($nodes_ids = array(1, 2)));

    $objects = array(
      20 => array('id' => 20, 'node_id' => 2),
      10 => array('id' => 10, 'node_id' => 1),
    );

    $sorted_objects = array(
      2 => array('id' => 20, 'node_id' => 2),
      1 => array('id' => 10, 'node_id' => 1),
    );

    $counter = 0;
  	$fetcher->setReturnValue('fetch_by_ids', $objects, array(array(10, 20), 'some_loader', $counter, $params = array('use_node_ids_as_keys' => true), 'fetch_by_ids'));
  	$this->assertIdentical($sorted_objects, $fetcher->fetch_by_node_ids($nodes_ids, 'some_loader', $counter, $params));

  	$fetcher->tally();
  }

  function test_fetch_one_by_id_with_cache()
  {
    $fetcher = new special_fetcher2($this);

    $object_id = 20;
    $objects = array($object_id => array('id' => $object_id, 'path' => 'some_path', 'node_id' => 10));

    $fetcher->setReturnValue('_get_object_class_name_by_id', 'loader', array($object_id));
    $fetcher->setReturnValue('fetch_by_ids', $objects, array(array($object_id), 'loader', $counter = 0));

    $fetcher->expectOnce('fetch_by_ids');
    $fetcher->expectOnce('_get_object_class_name_by_id');

    $fetcher->fetch_one_by_id($object_id); //for caching

    $this->assertEqual(reset($objects), $fetcher->fetch_one_by_id($object_id));

    $fetcher->tally();
  }

  function test_fetch_one_by_node_id_with_cache()
  {
    $fetcher = new special_fetcher2($this);
  	$fetcher->setReturnValue('_get_tree', $this->tree);

    $object_id = 20;
    $objects = array($object_id => array('id' => $object_id, 'path' => 'some_path', 'node_id' => 10));
    $node = array('id' => 10, 'object_id' => 20);

    $this->tree->setReturnValue('get_node', $node, $node_id = 10);

    $fetcher->expectOnce('fetch_by_ids');
    $fetcher->expectOnce('_get_object_class_name_by_id');

    $fetcher->setReturnValue('_get_object_class_name_by_id', 'loader', array($object_id));
    $fetcher->setReturnValue('fetch_by_ids', $objects, array(array($object_id), 'loader', $counter = 0));

    $fetcher->fetch_one_by_node_id($node_id);
    $this->assertEqual(reset($objects), $fetcher->fetch_one_by_node_id($node_id));

    $fetcher->tally();
  }

  function test_fetch_one_by_path_with_cache()
  {
    $fetcher = new special_fetcher2($this);
  	$fetcher->setReturnValue('_get_tree', $this->tree);

    $object_id = 20;
    $objects = array($object_id => array('id' => $object_id, 'path' => 'some_path', 'node_id' => $node_id = 10));
    $node = array('id' => 10, 'object_id' => 20);

    $this->tree->setReturnValue('get_node_by_path', $node, 'some_path');

    $fetcher->expectOnce('fetch_by_ids');
    $fetcher->expectOnce('_get_object_class_name_by_id');

    $fetcher->setReturnValue('_get_object_class_name_by_id', 'loader', array($object_id));
    $fetcher->setReturnValue('fetch_by_ids', $objects, array(array($object_id), 'loader', $counter = 0));

    $fetcher->fetch_one_by_path($node_id);
    $this->assertEqual(reset($objects), $fetcher->fetch_one_by_path($node_id));

    $fetcher->tally();
  }

  function test_fetch_requested_object()
  {
    $fetcher = new special_fetcher3($this);

    $node = array('id' => 10, 'object_id' => $object_id = 20);
    $object = array('id' => $object_id, 'path' => 'some_path', 'node_id' => $node_id = 10);

    $fetcher->setReturnValue('map_request_to_node', $node, array(new IsAExpectation('Mockrequest')));
    $fetcher->setReturnValue('fetch_one_by_id', $object, array(20));

    $this->assertEqual($object, $fetcher->fetch_requested_object($this->request));
    $fetcher->tally();
  }

  function test_fetch_sub_branch()
  {
    $fetcher = new special_fetcher3($this);
  	$fetcher->setReturnValue('_get_tree', $this->tree);

    $nodes = array(
      1 => array('id' => 1, 'object_id' => 10),
      2 => array('id' => 2, 'object_id' => 20),
    );

    $params = array(
      'depth' => $depth = true,
      'include_parent' => $include_parent = true,
      'check_expanded_parents' => $check_expanded_parents = true,
      'only_parents' => $only_parents = true,
    );

    $this->tree->setReturnValue('get_sub_branch_by_path', $nodes, array('some_path', $depth, $include_parent, $check_expanded_parents, $only_parents));

    $fetcher->setReturnValue('fetch_by_ids', $objects = array(10, 20), array(array(10, 20), 'loader', $counter = 0, $params, 'fetch_by_ids'));

    $this->assertEqual($objects, $fetcher->fetch_sub_branch('some_path', 'loader', $counter, $params));

    $fetcher->tally();
  }

  function test_fetch_sub_branch_no_such_path_with_params_by_default()
  {
    $fetcher = new special_fetcher3($this);
  	$fetcher->setReturnValue('_get_tree', $this->tree);

    $this->tree->setReturnValue('get_sub_branch_by_path', array(), array('some_path', 1, false, false, false));

    $fetcher->expectNever('fetch_by_ids');

    $this->assertEqual(array(), $fetcher->fetch_sub_branch('some_path', 'loader', $counter = 0, array()));

    $fetcher->tally();
  }

  function test_fetch_sub_branch_depth()
  {
  }

  function test_fetch_sub_branch_no_class_restriction()
  {
  }
}

?>