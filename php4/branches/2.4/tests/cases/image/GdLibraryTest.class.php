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
require_once(LIMB_DIR . '/class/image/ImageGd.class.php');
require_once(LIMB_DIR . 'tests/cases/image/ImageLibraryTest.class.php');

class GdLibraryTest extends ImageLibraryTest
{
  var $rotated_size = 4479;
  var $hflipped_size = 4011;
  var $wflipped_size = 3932;
  var $cutted_size1 = 1403;
  var $cutted_size2 = 4722;
  var $cutted_size3 = 1243;
  var $cutted_size4 = 1931;

  function GdLibraryTest()
  {
    $this->library = new ImageGd();

    parent :: ImageLibraryTest();
  }
}
?>