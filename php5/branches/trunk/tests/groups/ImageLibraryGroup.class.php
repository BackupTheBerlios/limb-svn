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
class ImageLibraryGroup extends LimbGroupTest
{
  function imageLibraryGroup()
  {
    $this->limbGroupTest('image library tests');
  }

  function getTestCasesHandles()
  {
    return array(LIMB_DIR . '/tests/cases/image/GdLibraryTest');
  }
}
?>