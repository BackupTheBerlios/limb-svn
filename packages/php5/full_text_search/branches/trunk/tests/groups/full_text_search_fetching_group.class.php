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
class full_text_search_fetching_group extends LimbGroupTest 
{
	function full_text_search_fetching_group() 
	{
	  $this->LimbGroupTest('full text search fetching tests');
	}
	
	function getTestCasesHandles()
	{
	  return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/../cases/fetching/');
	}	
}
?>