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
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.interface.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/lib/http/uri.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('tree');
Mock :: generate('uri');
Mock :: generate('request');

class requested_object_datasource_test extends LimbTestCase
{
  var $toolkit;
  var $request;
  var $uri;
  var $tree;
  var $datasource;

  function setUp()
  {
    $this->tree = new Mocktree($this);
    $this->request = new Mockrequest($this);
    $this->uri = new Mockuri($this);
    $this->datasource = new requested_object_datasource();

    $this->request->setReturnValue('get_uri', $this->uri);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getTree', $this->tree);
    $this->toolkit->setReturnValue('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->datasource->reset();

    $this->uri->tally();
    $this->request->tally();
    $this->tree->tally();
    $this->toolkit->tally();

    Limb :: popToolkit();
  }

  function test_map_uri_to_node_by_node_id()
  {
    $this->uri->setReturnValue('get_query_item', $node_id = 10, array('node_id'));

    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', $node = array('node_id' => $node_id), array($node_id));
    $this->assertEqual($node, $this->datasource->map_uri_to_node($this->uri));
  }

  function test_map_uri_to_node_by_path()
  {
    $this->uri->setReturnValue('get_query_item', false, array('node_id'));
    $this->uri->setReturnValue('get_path', $path = '/path');

    $this->tree->expectOnce('get_node_by_path');
    $this->tree->setReturnValue('get_node_by_path', $node = array('node_id' => 10), array($path, '/', false));

    $this->assertEqual($node, $this->datasource->map_uri_to_node($this->uri));
  }

  function test_map_request_to_node_by_node_id()
  {
    $this->request->setReturnValue('get', $node_id = 10, array('node_id'));
    $this->tree->setReturnValue('get_node', $node = array('node_id' => $node_id), array($node_id));

    $this->assertEqual($node, $this->datasource->map_request_to_node($this->request));
  }

  function test_map_request_to_node_by_path()
  {
    $this->uri->setReturnValue('get_query_item', false, array('node_id'));
    $this->uri->setReturnValue('get_path', $path = '/path');

    $this->request->setReturnValue('get', false, array('node_id'));

    $this->tree->expectOnce('get_node_by_path');
    $this->tree->setReturnValue('get_node_by_path', $node = array('node_id' => 10), array($path, '/', false));
    $this->assertEqual($node, $this->datasource->map_request_to_node($this->request));
  }

  function test_map_request_to_node_cache()
  {
    $this->request->expectOnce('get');

    $this->request->setReturnValue('get', $node_id = 10, array('node_id'));
    $this->tree->setReturnValue('get_node', $node = array('node_id' => $node_id), array($node_id));

    $this->datasource->map_request_to_node($this->request);
    $this->assertEqual($node, $this->datasource->map_request_to_node($this->request));
  }

  function test_get_object_ids_no_request()
  {
    try
    {
      $this->datasource->get_object_ids();
      $this->assertTrue(false);
    }
    catch(LimbException $e)
    {
    }

  }

  function test_get_object_ids()
  {
    Mock :: generatePartial('requested_object_datasource',
                            'requested_object_get_ids_test_version_datasource',
                            array('map_request_to_node'));

    $datasource = new requested_object_get_ids_test_version_datasource($this);
    $node = array('id' => 10, 'object_id' => $object_id = 20);

    $datasource->setReturnValue('map_request_to_node', $node, array(new IsAExpectation('Mockrequest')));
    $datasource->expectOnce('map_request_to_node');

    $datasource->set_request($this->request);
    $this->assertEqual(array($object_id), $datasource->get_object_ids());

    $datasource->tally();
  }
}

?>