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
require_once(dirname(__FILE__) . '/../../../site_objects/image_object.class.php');
require_once(dirname(__FILE__) . '/../../../image_variation.class.php');
require_once(dirname(__FILE__) . '/../../../media_manager.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

Mock :: generate('image_variation');

class image_object_test extends LimbTestCase 
{ 
	var $variation;
  var $image_object;
  
  function setUp()
  {
  	$this->variation = new Mockimage_variation($this);    
    $this->image_object = new image_object();  	
  }
  
  function tearDown()
  { 
    $this->variation->tally();
  }

  function test_get_variations_empty()
  {
    $this->assertTrue(!$this->image_object->get_variations());
  }
  
  function test_get_variation_failed()
  {
    $this->assertNull($this->image_object->get_variation('original'));
  }
      
  function test_attach_variation()
  { 
    $this->variation->expectOnce('get_name');
    $this->variation->setReturnValue('get_name', $name = 'original');
    
    $this->image_object->attach_variation($this->variation);
       
    $this->assertTrue($this->image_object->get_variation($name) === $this->variation);
    
    $this->assertTrue($this->image_object->get_variations() === array($name => $this->variation));
  }
}

?>