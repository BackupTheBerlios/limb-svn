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
require_once(LIMB_DIR . 'core/lib/image/image_netpbm.class.php');

class test_netpbm_library extends test_image_library 
{
  var $netpbm_dir = '';
  var $rotated_size = 5576;
  var $hflipped_size = 3861;
  var $wflipped_size = 3908;
  var $cutted_size1 = 1339;
  var $cutted_size2 = 4652;
  var $cutted_size3 = 1177;
  var $cutted_size4 = 1867;
  
  function test_netpbm_library() 
  {
  	$this->netpbm_dir = LIMB_DIR . '/tests/cases/image/netpbm/';
  	
  	$this->library =& new image_netpbm($this->netpbm_dir);

  	parent :: test_image_library();
  }
}
?>