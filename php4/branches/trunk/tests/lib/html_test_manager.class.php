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

class HTMLTestManager extends TestManager
{
	var $_url;

	function HTMLTestManager()
	{
		$this->_url = $_SERVER['PHP_SELF'];
	} 

	function getBaseURL()
	{
		return $this->_url;
	} 

  function displayTestCases(&$group, $path='')
  {
    $manager = &new HTMLTestManager();
    
    if (is_a($group, 'LimbGroupTest'))
		  $group_tests = $group->getTestCasesHandles();
		else  
		  $group_tests = array();

    $buffer = "<br><a href='" . $manager->getBaseURL() . "?run={$path}'>Run all tests from this group</a>\n";
    
		$buffer .= "<p>Available test groups in '" . $group->getLabel() . "':</p>\n";
		
		if (sizeof($group_tests))
		{
		  $buffer .= "<ul>";
  		foreach ($group_tests as $index => $group_test)
  		{
  		  resolve_handle($group_test);
  		  
  		  if(!is_a($group_test, 'LimbGroupTest'))
  			{
  			  $buffer .= "<li><a href='" . $manager->getBaseURL() . "?run={$path}/{$index}'>" . $group_test->getLabel() . "</a></li>\n";
  			}
  			else
  			{
  			  $buffer .= "<li><a href='" . $manager->getBaseURL() . "?list={$path}/{$index}'>" . $group_test->getLabel() . "</a></li>\n";
  			}  
  		} 
  		$buffer .= "</ul>\n";
  	}
  	else
  		$buffer .= "<p>No groups available.</p> \n";
		
    echo $buffer;
  }
} 
?>