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
class template_components_group extends LimbGroupTest 
{
  function template_components_group() 
  {
    $this->LimbGroupTest('template components tests');
  }
  
	function getTestCasesHandles()
	{
 		return TestManager::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/components');
	}  
}
?>