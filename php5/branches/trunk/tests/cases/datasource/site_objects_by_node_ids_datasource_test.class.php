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
require_once(LIMB_DIR . '/class/core/datasources/site_objects_by_node_ids_datasource.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
                
Mock :: generatePartial('site_objects_by_node_ids_datasource', 
                        'site_objects_by_node_ids_test_version_datasource',
                        array('_do_parent_fetch'));

Mock :: generate('tree');
Mock :: generate('LimbToolkit');

class site_objects_by_node_ids_datasource_test extends LimbTestCase
{
	var $datasource;
	var $tree;
  var $toolkit;

  function setUp()
  {
  	$this->datasource = new site_objects_by_node_ids_test_version_datasource($this);

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
    $node_ids = array(10, 11);
    $this->datasource->set_node_ids($node_ids);
    
    $this->tree->expectOnce('get_nodes_by_ids', array($node_ids));
    $this->tree->setReturnValue('get_nodes_by_ids', array(), array($node_ids));
    $this->assertEqual($this->datasource->get_object_ids(), array());
  }
  
  function test_get_object_ids()
  {
    $node_ids = array(100, 101);
    $this->datasource->set_node_ids($node_ids);
    
    $this->tree->expectOnce('get_nodes_by_ids', array($node_ids));
    
    $nodes = array(100 => array('object_id' => 20), 
                   101 => array('object_id' => 21));
    
    $this->tree->setReturnValue('get_nodes_by_ids', $nodes, array($node_ids));
    $this->assertEqual($this->datasource->get_object_ids(), array(20, 21));
  }

  function test_fetch_use_node_ids_as_keys()
  {
    $objects = array(20 => array('node_id' => 100),
                     21 => array('node_id' => 101));
     
    $this->datasource->setReturnValue('_do_parent_fetch', $objects);
    
    $this->datasource->set_use_node_ids_as_keys();
    
    $result_objects = array(100 => array('node_id' => 100),
                            101 => array('node_id' => 101));

    $this->assertEqual($this->datasource->fetch(), $result_objects);
  }
  
  function test_fetch_normal()
  {
    $objects = array(20 => array('node_id' => 100),
                     21 => array('node_id' => 101));
     
    $this->datasource->setReturnValue('_do_parent_fetch', $objects);

    $this->assertEqual($this->datasource->fetch(), $objects);
  }
}

?>