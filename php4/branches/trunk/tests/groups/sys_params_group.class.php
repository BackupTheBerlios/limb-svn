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

class sys_params_group extends LimbGroupTest
{
  function sys_params_group()
  {
    parent :: LimbGroupTest('sys params tests');
  }

  function & getTestCasesHandles()
  {
    return TestFinder :: getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/cases/sys_params');
  }
}
?>