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
require_once(LIMB_DIR . '/class/lib/image/image_netpbm.class.php');

class netpbm_library_test extends image_library_test 
{
  var $rotated_size = 5576;
  var $hflipped_size = 3861;
  var $wflipped_size = 3908;
  var $cutted_size1 = 1339;
  var $cutted_size2 = 4652;
  var $cutted_size3 = 1177;
  var $cutted_size4 = 1867;
  
  function netpbm_library_test() 
  {
  	$this->library = new image_netpbm();

  	parent :: image_library_test();
  }
}
?>