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
	  $this->addTestFile(TEST_CASES_DIR . '/tree/test_nested_tree_creation.php');
	  $this->addTestFile(TEST_CASES_DIR . '/tree/test_nested_tree_query.php');
	  $this->addTestFile(TEST_CASES_DIR . '/tree/test_nested_tree_manipulation.php');
	}
}
?>