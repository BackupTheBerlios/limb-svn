<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');

class unaffected_object 
{
  var $test_var = 'default';
}

class declared_in_same_file 
{
    var $test_var = 'default';
    
    function declared_in_same_file($var = 'construction_default') 
    {
        $this->test_var = $var;
    }
}

class resolve_handle_test extends LimbTestCase 
{  
  function test_null_handle() 
  {
	  $handle = null;
	  resolve_handle($handle);
		$this->assertNull($handle);  
  }
  
	function test_object_unaffected() 
	{
    $handle =& new unaffected_object();
    $obj =& $handle;
    $obj->test_var = 'changed';
    resolve_handle($handle);
		$this->assertIsA($handle, 'unaffected_object');
		$this->assertIdentical($handle, $obj);
		$this->assertEqual($handle->test_var, 'changed');
	}
	
  function test_class_declared_in_same_file() 
  {
    $handle = 'declared_in_same_file';
    resolve_handle($handle);
  	$this->assertIsA($handle, 'declared_in_same_file');
  }	
  
  function test_load_class_file1() 
  {
    $this->assertFalse(class_exists('loaded_handle_class'));
    $handle = dirname(__FILE__) . '/handle.inc.php|loaded_handle_class';
    resolve_handle($handle);
    $this->assertIsA($handle, 'loaded_handle_class');
    $this->assertTrue(class_exists('loaded_handle_class'));
  }  

  function test_load_class_file2() 
  {
    $this->assertFalse(class_exists('test_handle_class'));
    $handle = dirname(__FILE__) . '/test_handle_class';
    resolve_handle($handle);
    $this->assertIsA($handle, 'test_handle_class');
    $this->assertTrue(class_exists('test_handle_class'));
  }
  
  function test_constructor() 
  {
    $handle = array('declared_in_same_file', 'construction_parameter');
    resolve_handle($handle);
	  $this->assertIsA($handle, 'declared_in_same_file');
	  $this->assertEqual($handle->test_var, 'construction_parameter');
  }
  

}
?>