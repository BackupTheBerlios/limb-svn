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
require_once(LIMB_DIR . '/class/core/datasources/SiteObjectsByNodeIdsDatasource.class.php');
require_once(LIMB_DIR . '/class/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

Mock :: generatePartial('SiteObjectsByNodeIdsDatasource',
                        'SiteObjectsByNodeIdsTestVersionDatasource',
                        array('_doParentFetch'));

Mock :: generate('Tree');
Mock :: generate('LimbToolkit');

class SiteObjectsByNodeIdsDatasourceTest extends LimbTestCase
{
  var $datasource;
  var $tree;
  var $toolkit;

  function setUp()
  {
    $this->datasource = new SiteObjectsByNodeIdsTestVersionDatasource($this);

    $this->tree = new MockTree($this);

    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnReference('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->tree->tally();

    $this->datasource->reset();

    Limb :: popToolkit();
  }

  function testGetObjectIdsNoNodesFound()
  {
    $node_ids = array(10, 11);
    $this->datasource->setNodeIds($node_ids);

    $this->tree->expectOnce('getNodesByIds', array($node_ids));
    $this->tree->setReturnValue('getNodesByIds', array(), array($node_ids));
    $this->assertEqual($this->datasource->getObjectIds(), array());
  }

  function testGetObjectIds()
  {
    $node_ids = array(100, 101);
    $this->datasource->setNodeIds($node_ids);

    $this->tree->expectOnce('getNodesByIds', array($node_ids));

    $nodes = array(100 => array('object_id' => 20),
                   101 => array('object_id' => 21));

    $this->tree->setReturnValue('getNodesByIds', $nodes, array($node_ids));
    $this->assertEqual($this->datasource->getObjectIds(), array(20, 21));
  }

  function testFetchUseNodeIdsAsKeys()
  {
    $objects = array(20 => array('node_id' => 100),
                     21 => array('node_id' => 101));

    $this->datasource->setReturnValue('_doParentFetch', $objects);

    $this->datasource->setUseNodeIdsAsKeys();

    $result_objects = array(100 => array('node_id' => 100),
                            101 => array('node_id' => 101));

    $this->assertEqual($this->datasource->fetch(), $result_objects);
  }

  function testFetchNormal()
  {
    $objects = array(20 => array('node_id' => 100),
                     21 => array('node_id' => 101));

    $this->datasource->setReturnValue('_doParentFetch', $objects);

    $this->assertEqual($this->datasource->fetch(), $objects);
  }
}

?>