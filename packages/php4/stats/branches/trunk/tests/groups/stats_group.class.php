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
class stats_group extends LimbGroupTest 
{
	function stats_group() 
	{
	  $this->LimbGroupTest('stats tests');
	}
	
	function getTestCasesHandles()
	{
	  return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/../cases/stats');
	}
}
?>