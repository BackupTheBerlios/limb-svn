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
require_once(LIMB_DIR . '/class/core/datasources/SingleObjectDatasource.class.php');
require_once(LIMB_DIR . '/class/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Tree');

class SingleObjectDatasourceTest extends LimbTestCase
{
  var $toolkit;
  var $tree;
  var $datasource;

  function setUp()
  {
    $this->tree = new MockTree($this);
    $this->datasource = new SingleObjectDatasource();

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->datasource->reset();

    $this->tree->tally();
    $this->toolkit->tally();

    Limb :: popToolkit();
  }

  function testGetObjectIdsEmpty()
  {
    $this->assertEqual($this->datasource->getObjectIds(), array());
  }

  function testGetObjectIdsByObjectId()
  {
    $this->datasource->setObjectId($id = 200);

    $this->tree->expectNever('getNodeByPath');
    $this->tree->expectNever('getNode');

    $this->assertEqual($this->datasource->getObjectIds(), array($id));
  }

  function testGetObjectIdsByNodeId()
  {
    $this->datasource->setNodeId($node_id = 200);

    $this->tree->expectNever('getNodeByPath');
    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('objectId' => 10), array($node_id));

    $this->assertEqual($this->datasource->getObjectIds(), array(10));
  }

  function testGetObjectIdsByPath()
  {
    $this->datasource->setPath($path = '/test/path');

    $this->tree->expectNever('getNode');
    $this->tree->expectOnce('getNodeByPath');
    $this->tree->setReturnValue('getNodeByPath', array('objectId' => 10), array($path));

    $this->assertEqual($this->datasource->getObjectIds(), array(10));
  }

}

?>