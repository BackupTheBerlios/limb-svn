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
require_once(LIMB_DIR . '/class/core/datasources/site_objects_branch_datasource.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.interface.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');

Mock :: generate('tree');
Mock :: generate('LimbToolkit');

class site_objects_brach_datasource_test extends LimbTestCase
{
  var $datasource;
  var $tree;
  var $toolkit;

  function setUp()
  {
    $this->datasource = new site_objects_branch_datasource();

    $this->tree = new Mocktree($this);

    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnValue('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->tree->tally();

    $this->datasource->reset();

    Limb :: popToolkit();
  }

  function test_get_object_ids_no_nodes_found()
  {
    $this->datasource->set_path($path = '/root/news');
    $this->datasource->set_check_expanded_parents(true);
    $this->datasource->set_include_parent(false);
    $this->datasource->set_depth($depth = 3);

    $this->tree->expectOnce('get_sub_branch_by_path', array($path, $depth, false, true));
    $this->tree->setReturnValue('get_sub_branch_by_path', array(), array($path, $depth, false, true));
    $this->assertEqual($this->datasource->get_object_ids(), array());
  }

  function test_get_object_ids()
  {
    $this->datasource->set_path($path = '/root/news');
    $this->datasource->set_check_expanded_parents(true);
    $this->datasource->set_include_parent(false);
    $this->datasource->set_depth($depth = 3);

    $this->tree->expectOnce('get_sub_branch_by_path', array($path, $depth, false, true));

    $nodes = array(100 => array('object_id' => 20),
                   101 => array('object_id' => 21));

    $this->tree->setReturnValue('get_sub_branch_by_path', $nodes, array($path, $depth, false, true));
    $this->assertEqual($this->datasource->get_object_ids(), array(20, 21));
  }
}

?>