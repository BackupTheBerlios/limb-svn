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
class search_group extends LimbGroupTest 
{
	function search_group() 
	{
	  $this->LimbGroupTest('search tests');
	}
	
	function getTestCasesHandles()
	{
	  if(get_ini_option('common.ini', 'type', 'DB') == 'mysql')
 		  return TestFinder::getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/cases/full_text_search');
 		else
 		  return array();
	}		
}
?>