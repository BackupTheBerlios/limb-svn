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
require_once(dirname(__FILE__) . '/test_manager.class.php');

class CLITestManager extends TestManager
{
  function displayTestCases(&$group, $path='')
  {
		$group_tests = $group->getTestCasesHandles();

		$buffer = "Available test cases in " . $group->getLabel() . " :\n";
		foreach ($group_tests as $index => $group_test)
		{
		  resolve_handle($group_test);
		  
			$buffer .= $path . '/' . $index . ")  " . $group_test->getLabel() . "\n";
		} 
		$buffer .= "\n";
		
		print $buffer;
  }
} 
?>