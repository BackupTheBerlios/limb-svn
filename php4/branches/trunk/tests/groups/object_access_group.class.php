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
class object_access_group extends LimbGroupTest 
{
	function object_access_group() 
	{
	  $this->LimbGroupTest('object access tests');
	}
	
	function getTestCasesHandles()
	{
 		return TestManager::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/access');
	} 	
}
?>