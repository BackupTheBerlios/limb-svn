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
class file_resolver_group extends GroupTest 
{
	function file_resolver_group() 
	{
	  $this->GroupTest('file resolvers tests');
	  
 		TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/file_resolver');
	}
}
?>