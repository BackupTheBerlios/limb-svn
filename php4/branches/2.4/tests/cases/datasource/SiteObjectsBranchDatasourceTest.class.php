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
require_once(LIMB_DIR . '/class/core/datasources/SiteObjectsBranchDatasource.class.php');
require_once(LIMB_DIR . '/class/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

Mock :: generate('Tree');
Mock :: generate('LimbToolkit');

class SiteObjectsBrachDatasourceTest extends LimbTestCase
{
  var $datasource;
  var $tree;
  var $toolkit;

  function setUp()
  {
    $this->datasource = new SiteObjectsBranchDatasource();

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
    $this->datasource->setPath($path = '/root/news');
    $this->datasource->setCheckExpandedParents(true);
    $this->datasource->setIncludeParent(false);
    $this->datasource->setDepth($depth = 3);

    $this->tree->expectOnce('getSubBranchByPath', array($path, $depth, false, true));
    $this->tree->setReturnValue('getSubBranchByPath', array(), array($path, $depth, false, true));
    $this->assertEqual($this->datasource->getObjectIds(), array());
  }

  function testGetObjectIds()
  {
    $this->datasource->setPath($path = '/root/news');
    $this->datasource->setCheckExpandedParents(true);
    $this->datasource->setIncludeParent(false);
    $this->datasource->setDepth($depth = 3);

    $this->tree->expectOnce('getSubBranchByPath', array($path, $depth, false, true));

    $nodes = array(100 => array('object_id' => 20),
                   101 => array('object_id' => 21));

    $this->tree->setReturnValue('getSubBranchByPath', $nodes, array($path, $depth, false, true));
    $this->assertEqual($this->datasource->getObjectIds(), array(20, 21));
  }
}

?>