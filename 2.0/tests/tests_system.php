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


class tests_system extends GroupTest 
{
	function tests_system() 
	{
	  $this->GroupTest('system tests');
	  $this->addTestFile(TEST_CASES_DIR . '/system/test_dir.php');
	}
}
?>