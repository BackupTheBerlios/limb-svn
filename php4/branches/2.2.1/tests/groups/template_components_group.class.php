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


class template_components_group extends GroupTest 
{
  function template_components_group() 
  {
    $this->GroupTest('template components tests');
    TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/components');
  }
}
?>