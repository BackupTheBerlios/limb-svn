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
class db_group extends LimbGroupTest 
{
	function db_group() 
	{
	  $this->LimbGroupTest('db tests');
	}
	
	function getTestCasesHandles()
	{
		$handles = array();
 		$handles = TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/db');
 		
 		$db_type = get_ini_option('common.ini', 'type', 'DB');
 		
 		$handles = array_merge(
 			$handles,
 			TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/db/' . $db_type)
 		);
 		
 		return $handles;
	}
	
}
?>