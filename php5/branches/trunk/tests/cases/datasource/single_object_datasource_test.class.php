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
require_once(LIMB_DIR . '/class/core/datasources/single_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.interface.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generate('tree');

class single_object_datasource_test extends LimbTestCase
{
  var $toolkit;
  var $tree;
  var $datasource;

  function setUp()
  {
    $this->tree = new Mocktree($this);
    $this->datasource = new single_object_datasource();

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

  function test_get_object_ids_empty()
  {
    $this->assertEqual($this->datasource->get_object_ids(), array());
  }

  function test_get_object_ids_by_object_id()
  {
    $this->datasource->set_object_id($id = 200);

    $this->tree->expectNever('get_node_by_path');
    $this->tree->expectNever('get_node');

    $this->assertEqual($this->datasource->get_object_ids(), array($id));
  }

  function test_get_object_ids_by_node_id()
  {
    $this->datasource->set_node_id($node_id = 200);

    $this->tree->expectNever('get_node_by_path');
    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', array('object_id' => 10), array($node_id));

    $this->assertEqual($this->datasource->get_object_ids(), array(10));
  }

  function test_get_object_ids_by_path()
  {
    $this->datasource->set_path($path = '/test/path');

    $this->tree->expectNever('get_node');
    $this->tree->expectOnce('get_node_by_path');
    $this->tree->setReturnValue('get_node_by_path', array('object_id' => 10), array($path));

    $this->assertEqual($this->datasource->get_object_ids(), array(10));
  }

}

?>