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
class tests_controllers extends GroupTest 
{
	function tests_controllers() 
	{
	  $this->GroupTest('controllers tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/controllers');
	}
}
?>