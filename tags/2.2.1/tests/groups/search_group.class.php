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
class search_group extends GroupTest 
{
	function search_group() 
	{
	  $this->GroupTest('search tests');
	  
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/full_text_search');
	}
}
?>