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

class compiler_group extends GroupTest 
{
  function compiler_group() 
  {
    $this->GroupTest('compiler tests');
    //$this->addTestFile(LIMB_DIR . '/tests/cases/test_compiler_codewriter.php');
  }
}
?>