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

class tests_tree extends GroupTest 
{
	function tests_tree() 
	{
	  $this->GroupTest('tree');
	  
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/tree/materialized_path/');
	}
}
?>