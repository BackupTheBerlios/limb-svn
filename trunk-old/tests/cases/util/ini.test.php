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
require_once(LIMB_DIR . 'core/lib/util/ini.class.php');

class test_ini extends UnitTestCase 
{ 
	var $ini = null;
	 	
  function test_ini() 
  {
  	parent :: UnitTestCase();
  }
    
  function test_instance()
  {
  	$ini =& ini :: instance('ini_test.ini', LIMB_DIR . '/tests/cases/util/', false);
  	
  	$this->assertEqual($ini->root_dir(), LIMB_DIR . '/tests/cases/util/');
  	
  	$this->assertNotNull($ini);
  	$this->assertIsA($ini, 'ini');
  	
  	$ini2 =& ini :: instance('ini_test.ini', LIMB_DIR . '/tests/cases/util/');
  	
  	$this->assertReference($ini, $ini2);  	
  }
  
  function test_exists()
  {
  	$ini =& ini :: instance('ini_test.ini', LIMB_DIR . '/tests/cases/util/', false);
  	
  	$this->assertFalse($ini->has_group(''));
  	$this->assertTrue($ini->has_group('test'));
  	$this->assertTrue($ini->has_group('test2'));
  	$this->assertTrue($ini->has_group('empty_group'));
  	
  	$this->assertFalse($ini->has_variable('', 'no_variable'));
		$this->assertTrue($ini->has_variable('test', 'test'));
		$this->assertTrue($ini->has_variable('default', 'unassigned_value'));
  }
  
  function test_variable()
  {
  	$ini =& ini :: instance('ini_test.ini', LIMB_DIR . '/tests/cases/util/', false);
  	
  	$val = $ini->variable('default', 'unassigned_value');
  	$this->assertTrue(empty($val));
  	
  	$val = $ini->variable('default', 'test');
  	$this->assertFalse(empty($val));
  	  	
  	$this->assertEqual($ini->variable('', 'no_variable'), '');
  	
  	$this->assertErrorPattern('/undefined block/');
  	
  	$this->assertEqual($ini->variable('test', 'test'), 1);
  	$this->assertEqual($ini->variable('test2', 'test1'), 2);
  	
  	$var = $ini->variable('test3', 'test');
  	$this->assertTrue(is_array($var) && (sizeof($var) == 6));
  	
  	$this->assertTrue(isset($var['wow']));
  	$this->assertEqual($var['wow'], 6);
  	$this->assertTrue($var['hey'], 7);
  }
      
  function test_assign()
  {
  	$ini =& ini :: instance('ini_test.ini', LIMB_DIR . '/tests/cases/util/', false);
  	
  	$this->assertTrue($ini->assign('test', 'test', $test));
  	$this->assertEqual($test, 1);
  	$this->assertFalse($ini->assign('test', 'no_variable', $test));
  }
  
  function test_group()
  {
  	$ini =& ini :: instance('ini_test.ini', LIMB_DIR . '/tests/cases/util/', false);

  	$this->assertNull($ini->group('no_group'));
  	
  	$this->assertErrorPattern('/unknown block/');
  	
  	$this->assertNotNull($ini->group('default'));
  	
  	$group = $ini->group('test2');
  	
  	$this->assertTrue(is_array($group) && (sizeof($group) == 3));
  	$this->assertEqual($group['test1'], 2);
  	$this->assertEqual($group['test2'], 3);
  	$this->assertEqual($group['test3'], "  #It's just a \"test\"!#  ");    	
  }
}

?>