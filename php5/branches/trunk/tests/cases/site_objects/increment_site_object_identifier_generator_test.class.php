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
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/increment_site_object_identifier_generator.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generate('site_object');
Mock :: generate('tree');

class increment_site_object_identifier_generator_test extends LimbTestCase 
{ 
	var $object;
  var $generator;
  var $tree;
  var $toolkit;
  
  function setUp()
  {
  	$this->object = new Mocksite_object($this);
    $this->generator = new IncrementSiteObjectIdentifierGenerator();
    $this->tree = new Mocktree($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getTree', $this->tree);
    
    $this->object->expectOnce('get_parent_node_id');
    $this->object->setReturnValue('get_parent_node_id', 100);
    
    $this->tree->expectOnce('get_max_child_identifier', array(100));
    
    Limb :: registerToolkit($this->toolkit);    
  }
  
  function tearDown()
  { 
  	$this->object->tally();
    $this->tree->tally();
    $this->toolkit->tally();
    
    Limb :: popToolkit();
  }

  function test_generate_false()
  {    
    $this->tree->setReturnValue('get_max_child_identifier', false); 
    $this->assertIdentical($this->generator->generate($this->object), false);		
  }

  function test_generate_for_number()
  {    
    $this->tree->setReturnValue('get_max_child_identifier', 0); 
    $this->assertEqual($this->generator->generate($this->object), 1);		
  }

  function test_generate_for_number2()
  {    
    $this->tree->setReturnValue('get_max_child_identifier', 1000); 
    $this->assertEqual($this->generator->generate($this->object), 1001);		
  }

  function test_generate_for_text()
  {
    $this->tree->setReturnValue('get_max_child_identifier', 'ru');
    $this->assertEqual($this->generator->generate($this->object), 'ru1');  	
  }
  
  function test_generate_for_text2()
  {
    $this->tree->setReturnValue('get_max_child_identifier', '119');
    $this->assertEqual($this->generator->generate($this->object), '120');  	
  }  	      	  

  function test_generate_for_text_ending_with_number()
  {
    $this->tree->setReturnValue('get_max_child_identifier', 'test10');
    $this->assertEqual($this->generator->generate($this->object), 'test11');  	
  }

  function test_generate_for_text_ending_with_number2()
  {
    $this->tree->setReturnValue('get_max_child_identifier', '4test19');
    $this->assertEqual($this->generator->generate($this->object), '4test20');  	
  }
  
  function test_generate_for_text_ending_with_number3()
  {
    $this->tree->setReturnValue('get_max_child_identifier', '4te10st19');
    $this->assertEqual($this->generator->generate($this->object), '4te10st20');  	
  }  
}

?>