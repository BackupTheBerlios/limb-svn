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
require_once(LIMB_DIR . 'class/lib/image/image_gd.class.php');
require_once(LIMB_DIR . 'tests/cases/image/_image_library_test.class.php');

class gd_library_test extends image_library_test 
{
  var $rotated_size = 4479;
  var $hflipped_size = 4011;
  var $wflipped_size = 3932;
  var $cutted_size1 = 1403;
  var $cutted_size2 = 4722;
  var $cutted_size3 = 1243;
  var $cutted_size4 = 1931;
  
  function gd_library_test() 
  {
  	$this->library = new image_gd();

  	parent :: image_library_test();
  }
}
?>