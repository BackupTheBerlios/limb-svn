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
class validation_group extends LimbGroupTest 
{
	function validation_group() 
	{
	  $this->LimbGroupTest('validation tests');
	}
	
	function getTestCasesHandles()
	{
 		return TestManager::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/validation');
	}		
}
?>