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
class tests_actions extends GroupTest 
{
	function tests_actions() 
	{
	  $this->GroupTest('actions tests');
	  $this->addTestFile(TEST_CASES_DIR . '/actions/test_action_factory.php');
	  $this->addTestFile(TEST_CASES_DIR . '/actions/test_action.php');
	  $this->addTestFile(TEST_CASES_DIR . '/actions/test_form_action.php');
	}
}
?>