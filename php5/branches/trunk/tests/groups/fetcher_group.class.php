<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class fetcher_group extends LimbGroupTest 
{
	function fetcher_group() 
	{
	  $this->LimbGroupTest('fetcher tests');
	}

	function getTestCasesHandles()
	{
 		return TestFinder::getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/cases/fetcher');
	}
}
?>