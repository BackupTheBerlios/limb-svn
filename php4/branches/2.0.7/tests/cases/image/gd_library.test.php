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
require_once(LIMB_DIR . 'core/lib/image/image_gd.class.php');

class test_gd_library extends test_image_library 
{
  var $rotated_size = 4479;
  var $hflipped_size = 4011;
  var $wflipped_size = 3932;
  var $cutted_size1 = 1403;
  var $cutted_size2 = 4722;
  var $cutted_size3 = 1243;
  var $cutted_size4 = 1931;
  
  function test_gd_library() 
  {
  	$this->library =& new image_gd();

  	parent :: test_image_library();
  }
}
?>