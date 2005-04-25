<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: tree_group.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/

class i18n_group extends LimbGroupTest
{
  function i18n_group()
  {
    parent :: LimbGroupTest('i18n');
  }

  function & getTestCasesHandles()
  {
    return TestFinder :: getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/cases/i18n');
  }
}
?>