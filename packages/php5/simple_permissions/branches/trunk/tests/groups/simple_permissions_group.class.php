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
class simple_permissions_group extends LimbGroupTest 
{
	function simple_permissions_group() 
	{
	  $this->LimbGroupTest('simple permissions tests');
	}
	
	function getTestCasesHandles()
	{
	  return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/../cases');
	}	
}
?>