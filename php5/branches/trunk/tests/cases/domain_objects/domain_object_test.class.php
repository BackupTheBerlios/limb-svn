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
require_once(LIMB_DIR . '/class/core/domain_object.class.php');
require_once(LIMB_DIR . '/class/core/dataspace.class.php');

class domain_object_test extends LimbTestCase 
{
  var $object;
  
  function setUp()
  {
    $this->object = new domain_object();
  }
  
  function tearDown()
  {
  }
  
  function test_get_id()
  {
    $this->object->set_id(10);
    $this->assertEqual($this->object->get_id(), 10);
  }
         
  function test_is_dirty_false()
  {
    $this->assertFalse($this->object->is_dirty());
  }
    
  function test_object_becomes_clean_after_import()
  {
    $this->assertFalse($this->object->is_dirty());
    
    $this->object->set('test', 'value');
    
    $this->assertTrue($this->object->is_dirty());
    
    $values = array('test');
    
    $this->object->import($values);
    
    $this->assertFalse($this->object->is_dirty());
  }
  
  function test_object_becomes_dirty_after_set()
  {
    $this->assertFalse($this->object->is_dirty());
    
    $this->object->set('test', 'value');
    
    $this->assertTrue($this->object->is_dirty());    
  }
  
  function test_mark_clean()
  {
    $this->object->set('test', 'value');
    $this->object->mark_clean();
    
    $this->assertFalse($this->object->is_dirty());
  }

  function test_object_becomes_dirty_after_get_nonexisting_reference()
  {
    $property =& $this->object->get_reference('test');
    
    $this->assertTrue($this->object->is_dirty());    
  }

  function test_object_becomes_dirty_after_reference_got_changed1()
  {
    $this->object->import(array('test' => new object()));
    
    $obj = $this->object->get('test');
    
    $this->assertFalse($this->object->is_dirty());
    
    $obj->set('whatever', 1);
    
    $this->assertTrue($this->object->is_dirty()); 
  }
  
  function test_object_becomes_dirty_after_reference_got_changed2()
  {
    $this->object->import(array('test' => 2));
    
    $ref =& $this->object->get_reference('test');
    
    $this->assertFalse($this->object->is_dirty());
    
    $ref = 1; 
    
    $this->assertTrue($this->object->is_dirty()); 
  }
  
}

?>