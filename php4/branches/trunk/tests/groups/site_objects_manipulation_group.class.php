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

class site_objects_manipulation_group extends GroupTest 
{
	function site_objects_manipulation_group() 
	{
	  $this->GroupTest('site objects manipulation tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/site_objects_manipulation/');
	}
}
?>