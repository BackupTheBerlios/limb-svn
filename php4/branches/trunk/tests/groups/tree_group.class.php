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
class tree_group extends LimbGroupTest
{
	function tree_group() 
	{
	  $this->LimbGroupTest('tree');
	}
	
	function getTestCasesHandles()
	{
 		return TestManager::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/tree');
	} 	
}
?>