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
require_once(LIMB_DIR . 'class/lib/util/ini.class.php');

Mock :: generatePartial(
  'ini',
  'ini_mock_version_override',
  array('_parse', '_save_cache')
);

class ini_override_test extends LimbTestCase 
{ 
  function setUp()
  {
  	debug_mock :: init($this);
  }
  
  function tearDown()
  {
  	debug_mock :: tally();
  	clear_testing_ini();
  }
    
  function test_override()
  {
    $ini =& ini :: instance(LIMB_DIR . '/tests/cases/util/ini_test2.ini', false);
        
  	$this->assertTrue($ini->has_group('test'));
  	$this->assertTrue($ini->has_group('test2'));
  	
		$this->assertTrue($ini->has_option('test', 'test'));
		$this->assertTrue($ini->has_option('test2', 'test2'));
		$this->assertEqual($ini->get_option('test', 'test'), 2);
		$this->assertEqual($ini->get_option('test2', 'test2'), 2);
  }
  
  function test_cache_original_file_was_modified()
  {
  	register_testing_ini(
      'testing2.ini',
      'test = 1'
    );

  	register_testing_ini(
      'testing2.ini.override',
      'test = 2'
    );
    
    $ini =& new ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...    
    
    // caching happens very quickly we have to tweak the original file modification time
    // in order to test     
    touch($ini->get_original_file(), time()+100);
    touch($ini->get_override_file(), time()-100);
    
    $ini_mock =& new ini_mock_version_override($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_save_cache');
    
    $ini_mock->__construct(VAR_DIR . 'testing2.ini', true);
        
    $ini_mock->tally();
    
    $ini->reset_cache();
  }   

  function test_cache_override_file_was_modified()
  {
  	register_testing_ini(
      'testing2.ini',
      'test = 1'
    );

  	register_testing_ini(
      'testing2.ini.override',
      'test = 2'
    );
    
    $ini =& new ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...    
    
    // caching happens very quickly we have to tweak the original file modification time
    // in order to test     
    touch($ini->get_original_file(), time()-100);
    touch($ini->get_override_file(), time()+100);
    
    $ini_mock =& new ini_mock_version_override($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_save_cache');
    
    $ini_mock->__construct(VAR_DIR . 'testing2.ini', true);
        
    $ini_mock->tally();
    
    $ini->reset_cache();
  }  
}

?>