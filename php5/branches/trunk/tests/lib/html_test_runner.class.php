<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/test_runner.class.php');

class HTMLTestRunner extends TestRunner
{ 
  function &_getReporter()
  {
    return new HTMLReporter();
  }
  
  function getBaseUrl()
  {
    return $_SERVER['PHP_SELF'];
  }
  
  function run(&$root_group)
  {
    if(isset($_GET['perform']))
    {
      $path = $_GET['perform'];
      $this->perform($path, $root_group);
    }  
      
    if(isset($_GET['browse']))
    {
      $path = $_GET['browse'];
      $this->browse($path, $root_group);
    }
    elseif(!isset($_GET['perform']))
      $this->browse('', $root_group);  
  }
 
  function _displayBeforePerform($path, &$root_group, &$current_group)
  {    
    echo '';
  }
  
  function _displayAfterPerform($path, &$root_group, &$current_group)
  {
    if(isset($_GET['back']))  
      $postfix = '';
    else
      $postfix = '/..';
      
    echo '<p><a href="' . $this->getBaseURL() . '?browse=' . $path . $postfix . '">Back</a></p>';
  
    echo debug :: parse_html_console();
  }
  
  function _displayBrowse($path, &$root_group, &$current_group)
  {
    if($root_group != $current_group)
      echo '<p><a href="' . $this->getBaseURL() . '?browse=' . $path . '/..">Back</a></p>';
    
    if (is_a($current_group, 'LimbGroupTest'))
		  $group_tests = $current_group->getTestCasesHandles();
		else  
		  $group_tests = array();

    $buffer = "<br><a href='" . $this->getBaseURL() . "?perform={$path}&back=1'>Run all tests from this group</a>\n";
    
		$buffer .= "<p>Available test groups in '" . $current_group->getLabel() . "':</p>\n";
		
		if (sizeof($group_tests))
		{
		  $buffer .= "<ul>";
  		foreach ($group_tests as $index => $group_test)
  		{
  		  resolve_handle($group_test);
  		  
  		  if(!is_a($group_test, 'LimbGroupTest'))
  			{
  			  $buffer .= "<li><a href='" . $this->getBaseURL() . "?perform={$path}/{$index}'>P</a> " . $group_test->getLabel() . "</li>\n";
  			}
  			else
  			{
  			  $buffer .= "<li><a href='" . $this->getBaseURL() . "?perform={$path}/{$index}'>P</a> <a href='" . $this->getBaseURL() . "?browse={$path}/{$index}'>B</a> " . $group_test->getLabel() . "</li>\n";
  			}  
  		} 
  		$buffer .= "</ul>\n";
  	}
  	else
  		$buffer .= "<p>No groups available.</p> \n";
		
    echo $buffer;
    
    echo debug :: parse_html_console();
  }  
}

?>