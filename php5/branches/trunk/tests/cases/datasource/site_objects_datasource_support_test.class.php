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
require_once(LIMB_DIR . '/class/core/datasources/site_objects_datasource_support.inc.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
                      
Mock :: generate('site_object');
Mock :: generate('tree');
Mock :: generate('LimbToolkit');

class site_objects_datasource_support_test extends LimbTestCase
{
	var $site_object;
  var $tree;
  var $toolkit;

  function setUp()
  {
  	$this->site_object = new Mocksite_object($this);
    $this->tree = new Mocktree($this);
    $this->toolkit = new MockLimbToolkit($this);
    
    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
  	$this->site_object->tally();
    $this->tree->tally();
    $this->toolkit->tally();
    
    Limb :: popToolkit();
  }
  
  function test_assign_paths_to_site_objects_worst_case()
  {
    $this->toolkit->expectOnce('getTree');
    $this->toolkit->setReturnValue('getTree', $this->tree);
    
    $objects_array = array(array('parent_node_id' => 10, 'node_id' => 100, 'identifier' => '1'),
                           array('parent_node_id' => 20, 'node_id' => 200, 'identifier' => '2'),
                           array('parent_node_id' => 30, 'node_id' => 300, 'identifier' => '3'));
    
    $this->tree->expectCallCount('get_parents', 3);
    $this->tree->setReturnValueAt(0, 'get_parents', array(array('identifier' => '-1')), array(100));
    $this->tree->setReturnValueAt(1, 'get_parents', array(array('identifier' => '-2')), array(200));
    $this->tree->setReturnValueAt(2, 'get_parents', array(array('identifier' => '-3')), array(300));
     
    assign_paths_to_site_objects($objects_array);
    
    $this->assertEqual($objects_array[0]['path'], '/-1/1');
    $this->assertEqual($objects_array[1]['path'], '/-2/2');
    $this->assertEqual($objects_array[2]['path'], '/-3/3');
  }  

  function test_assign_paths_to_site_objects_parents_cache()
  {
    $this->toolkit->expectOnce('getTree');
    $this->toolkit->setReturnValue('getTree', $this->tree);
    
    $objects_array = array(array('parent_node_id' => 10, 'node_id' => 100, 'identifier' => '1'),
                           array('parent_node_id' => 10, 'node_id' => 200, 'identifier' => '2'),
                           array('parent_node_id' => 30, 'node_id' => 300, 'identifier' => '3'));
    
    $this->tree->expectCallCount('get_parents', 2);
    $this->tree->setReturnValueAt(0, 'get_parents', array(array('identifier' => '-1')), array(100));    
    $this->tree->setReturnValueAt(1, 'get_parents', array(array('identifier' => '-3')), array(300));
     
    assign_paths_to_site_objects($objects_array);
    
    $this->assertEqual($objects_array[0]['path'], '/-1/1');
    $this->assertEqual($objects_array[1]['path'], '/-1/2');
    $this->assertEqual($objects_array[2]['path'], '/-3/3');
  }  
  
  function test_assign_paths_to_site_objects_append()
  {
    $this->toolkit->expectOnce('getTree');
    $this->toolkit->setReturnValue('getTree', $this->tree);
    
    $objects_array = array(array('parent_node_id' => 10, 'node_id' => 100, 'identifier' => '1'),
                           array('parent_node_id' => 10, 'node_id' => 300, 'identifier' => '3'));
    
    $this->tree->setReturnValue('get_parents', array(array('identifier' => '-1')), array(100));    
     
    assign_paths_to_site_objects($objects_array, '-append');
    
    $this->assertEqual($objects_array[0]['path'], '/-1/1-append');
    $this->assertEqual($objects_array[1]['path'], '/-1/3-append');
  }   
  
  function test_wrap_with_site_object_empty_array()
  {
    $fetched_data = array();
    
    $this->toolkit->expectNever('createSiteObject');    
    
    $this->assertTrue(wrap_with_site_object($fetched_data) === false);
  }
  
  function test_wrap_with_site_object_single()
  {
    $fetched_data = array('class_name' => $class_name = 'test_site_object');
    
    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array($class_name));
    $this->toolkit->expectOnce('createSiteObject', array($class_name));
    
    $this->site_object->expectOnce('merge', array($fetched_data));
    
    $this->assertTrue(wrap_with_site_object($fetched_data) === $this->site_object);
  }

  function test_wrap_with_site_object_multiple()
  {
    $fetched_data = array($data1 = array('class_name' => $class_name1 = 'test_site_object1'),
                          $data2 = array('class_name' => $class_name2 = 'test_site_object2'));
    
    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array($class_name1));
    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array($class_name2));
    $this->toolkit->expectCallCount('createSiteObject', 2);
    
    $this->site_object->expectArgumentsAt(0, 'merge', array($data1));
    $this->site_object->expectArgumentsAt(1, 'merge', array($data2));
    
    $this->assertTrue(wrap_with_site_object($fetched_data) === array($this->site_object, $this->site_object));
    
  }
  
}

?>