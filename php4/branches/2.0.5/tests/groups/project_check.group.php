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
class tests_project_check extends GroupTest 
{
	function tests_project_check() 
	{
	  $this->GroupTest('project tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/project_check');
	}
}
?>