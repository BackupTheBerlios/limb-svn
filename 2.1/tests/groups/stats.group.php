<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: search.group.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
class tests_stats extends GroupTest 
{
	function tests_stats() 
	{
	  $this->GroupTest('stats tests');
	  
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/stats');
	}
}
?>