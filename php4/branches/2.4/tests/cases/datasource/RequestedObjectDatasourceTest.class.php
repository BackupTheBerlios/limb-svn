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
require_once(LIMB_DIR . '/class/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/class/tree/Tree.interface.php');
require_once(LIMB_DIR . '/class/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/request/Request.class.php');
require_once(LIMB_DIR . '/class/http/Uri.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Tree');
Mock :: generate('Uri');
Mock :: generate('Request');

class RequestedObjectDatasourceTest extends LimbTestCase
{
  var $toolkit;
  var $request;
  var $uri;
  var $tree;
  var $datasource;

  function setUp()
  {
    $this->tree = new MockTree($this);
    $this->request = new MockRequest($this);
    $this->uri = new MockUri($this);
    $this->datasource = new RequestedObjectDatasource();

    $this->request->setReturnReference('getUri', $this->uri);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('getTree', $this->tree);
    $this->toolkit->setReturnReference('getRequest', $this->request);

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

  function testMapUriToNodeByNodeId()
  {
    $this->uri->setReturnValue('getQueryItem', $node_id = 10, array('node_id'));

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', $node = array('node_id' => $node_id), array($node_id));
    $this->assertEqual($node, $this->datasource->mapUriToNode($this->uri));
  }

  function testMapUriToNodeByPath()
  {
    $this->uri->setReturnValue('getQueryItem', false, array('node_id'));
    $this->uri->setReturnValue('getPath', $path = '/path');

    $this->tree->expectOnce('getNodeByPath');
    $this->tree->setReturnValue('getNodeByPath', $node = array('node_id' => 10), array($path, '/', false));

    $this->assertEqual($node, $this->datasource->mapUriToNode($this->uri));
  }

  function testMapRequestToNodeByNodeId()
  {
    $this->request->setReturnValue('get', $node_id = 10, array('node_id'));
    $this->tree->setReturnValue('getNode', $node = array('node_id' => $node_id), array($node_id));

    $this->assertEqual($node, $this->datasource->mapRequestToNode($this->request));
  }

  function testMapRequestToNodeByPath()
  {
    $this->uri->setReturnValue('getQueryItem', false, array('node_id'));
    $this->uri->setReturnValue('getPath', $path = '/path');

    $this->request->setReturnValue('get', false, array('node_id'));

    $this->tree->expectOnce('getNodeByPath');
    $this->tree->setReturnValue('getNodeByPath', $node = array('node_id' => 10), array($path, '/', false));
    $this->assertEqual($node, $this->datasource->mapRequestToNode($this->request));
  }

  function testMapRequestToNodeCache()
  {
    $this->request->expectOnce('get');

    $this->request->setReturnValue('get', $node_id = 10, array('node_id'));
    $this->tree->setReturnValue('getNode', $node = array('node_id' => $node_id), array($node_id));

    $this->datasource->mapRequestToNode($this->request);
    $this->assertEqual($node, $this->datasource->mapRequestToNode($this->request));
  }

  function testGetObjectIdsNoRequest()
  {
    $this->datasource->getObjectIds();
    $this->assertTrue(catch('Exception', $e));
  }

  function testGetObjectIds()
  {
    Mock :: generatePartial('RequestedObjectDatasource',
                            'RequestedObjectGetIdsTestVersionDatasource',
                            array('mapRequestToNode'));

    $datasource = new RequestedObjectGetIdsTestVersionDatasource($this);
    $node = array('id' => 10, 'object_id' => $object_id = 20);

    $datasource->setReturnValue('mapRequestToNode', $node, array(new IsAExpectation('MockRequest')));
    $datasource->expectOnce('mapRequestToNode');

    $datasource->setRequest($this->request);
    $this->assertEqual(array($object_id), $datasource->getObjectIds());

    $datasource->tally();
  }
}

?>