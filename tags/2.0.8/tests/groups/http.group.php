<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: util.group.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/


class tests_http extends GroupTest 
{
	function tests_http() 
	{
	  $this->GroupTest('http tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/http');
	}
}
?>