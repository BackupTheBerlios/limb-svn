<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/image/ImageNetpbm.class.php');

class NetpbmLibraryTest extends ImageLibraryTest
{
  var $rotated_size = 5576;
  var $hflipped_size = 3861;
  var $wflipped_size = 3908;
  var $cutted_size1 = 1339;
  var $cutted_size2 = 4652;
  var $cutted_size3 = 1177;
  var $cutted_size4 = 1867;

  function netpbmLibraryTest()
  {
    $this->library = new ImageNetpbm();

    parent :: imageLibraryTest();
  }
}
?>