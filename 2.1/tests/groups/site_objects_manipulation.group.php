<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: db.group.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/

class tests_site_objects_manipulation extends GroupTest 
{
	function tests_site_objects_manipulation() 
	{
	  $this->GroupTest('site objects manipulation tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/site_objects_manipulation/');
	}
}
?>