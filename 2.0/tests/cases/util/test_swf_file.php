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

	
  require_once(LIMB_DIR . '/core/lib/util/swf_file.class.php');

  class test_swf_file extends UnitTestCase 
  {
    function test_swf_file() 
    {
    	parent :: UnitTestCase();
    }
    
    function test_save_swf() 
    {
      $swf = new swf_file();
      
      $res = $swf->load(TEST_CASES_DIR . '/util/swf/test.gz.swf');
      
      $this->assertTrue($res);
      $this->assertTrue($swf->loaded);
      
      $res = $swf->save(TEST_CASES_DIR . '/util/swf/test.new.swf');

      $this->assertTrue($res);
      
      $swf2 = new swf_file();

      $res = $swf2->load(TEST_CASES_DIR . '/util/swf/test.new.swf');
      
      $this->assertTrue($res);
      $this->assertTrue($swf2->loaded);
      
      if (is_file(TEST_CASES_DIR . '/util/swf/test.new.swf'))
        unlink(TEST_CASES_DIR . '/util/swf/test.new.swf');
    }
    
    function test_save_compressed_swf() 
    {
      $swf = new swf_file();
      
      $res = $swf->load(TEST_CASES_DIR . '/util/swf/test.swf');
      
      $this->assertTrue($res);
      $this->assertTrue($swf->loaded);
      
      $res = $swf->save(TEST_CASES_DIR . '/util/swf/test.new.gz.swf', true);

      $this->assertTrue($res);
      
      $swf2 = new swf_file();

      $res = $swf2->load(TEST_CASES_DIR . '/util/swf/test.new.gz.swf');
      
      $this->assertTrue($res);
      $this->assertTrue($swf2->loaded);
      
      if (is_file(TEST_CASES_DIR . '/util/swf/test.new.gz.swf'))
        unlink(TEST_CASES_DIR . '/util/swf/test.new.gz.swf');
    }
  }
  
?>