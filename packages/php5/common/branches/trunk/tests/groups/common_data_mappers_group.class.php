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
class common_data_mappers_group extends LimbGroupTest 
{
	function common_data_mappers_group() 
	{
	  $this->LimbGroupTest('common data mappers tests');
	}
	
	function getTestCasesHandles()
	{
 		return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/../cases/data_mappers');
	}
}

?>