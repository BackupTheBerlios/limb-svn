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
class tests_site_objects extends GroupTest 
{
	function tests_site_objects() 
	{
	  $this->GroupTest('site objects tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/site_objects');
	}
}
?>