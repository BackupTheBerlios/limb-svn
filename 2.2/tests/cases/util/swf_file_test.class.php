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

class swf_file_test extends UnitTestCase 
{      
  function tearDown()
  {
    if (is_file(VAR_DIR . '/test.new.swf'))
      unlink(VAR_DIR . '/test.new.swf');  

    if (is_file(VAR_DIR . '/test.new.gz.swf'))
      unlink(VAR_DIR . '/test.new.gz.swf');        
  }
  
  function test_save_swf() 
  {
    $swf = new swf_file();
    
    $res = $swf->load(LIMB_DIR . '/tests/cases/util/swf/test.gz.swf');
    
    $this->assertTrue($res);
    $this->assertTrue($swf->loaded);
    
    $res = $swf->save(VAR_DIR . '/test.new.swf');

    $this->assertTrue($res);
    
    $swf2 = new swf_file();

    $res = $swf2->load(LIMB_DIR . '/tests/cases/util/swf/test.new.swf');
    
    $this->assertTrue($res);
    $this->assertTrue($swf2->loaded);
    
  }
  
  function test_save_compressed_swf() 
  {
    $swf = new swf_file();
    
    $res = $swf->load(LIMB_DIR . '/tests/cases/util/swf/test.swf');
    
    $this->assertTrue($res);
    $this->assertTrue($swf->loaded);
    
    $res = $swf->save(VAR_DIR . '/test.new.gz.swf', true);

    $this->assertTrue($res);
    
    $swf2 = new swf_file();

    $res = $swf2->load(LIMB_DIR . '/tests/cases/util/swf/test.new.gz.swf');
    
    $this->assertTrue($res);
    $this->assertTrue($swf2->loaded);
  }
}

?>