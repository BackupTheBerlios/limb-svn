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
require_once(LIMB_DIR . '/tests/cases/limb_group_test.class.php');

class StatsRootGroupTest extends LimbGroupTest
{
	function StatsRootGroupTest() 
	{
	  $this->LimbGroupTest('stats package tests');
	}
	
	function getTestCasesHandles()
	{
 		return TestManager::getTestCasesHandlesFromDirectory(dirname(__FILE__) . '/groups');
	}
}

?>