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
ob_start();
require_once(dirname(__FILE__) . '/setup.php');
require_once(LIMB_DIR . '/tests/lib/html_test_manager.class.php');
require_once(LIMB_DIR . '/tests/cases/limb_group_test.class.php');

if (!include_once(SIMPLE_TEST . 'reporter.php'))
{
	error('runtime', 'LIBRARY_REQUIRED', array('library' => 'Simple Test',
			'path' => SIMPLE_TEST));
} 

class GlobalGroupTest extends LimbGroupTest
{
	function GlobalGroupTest() 
	{
	  $this->LimbGroupTest('all tests');
	}
	
	function getTestCasesHandles()
	{
 		return TestManager::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/groups');
	}
}

$global_group =& new GlobalGroupTest();
$test_manager = new HTMLTestManager();

if(isset($_GET['run']))
{
  $path = $_GET['run'];
  run_tests($path, $test_manager, $global_group);
}  
  
if(isset($_GET['list']))
{
  $path = $_GET['list'];
  list_tests($path, $test_manager, $global_group);
}
elseif(!isset($_GET['run']))
  list_tests('', $test_manager, $global_group);

function run_tests($path, &$test_manager, &$global_group)
{
  $current_group = $test_manager->getCaseByPath($path, $global_group);
  
  $reporter = new HTMLReporter();
  
  $test_manager->run($current_group, $reporter);

  echo '<p><a href="' . $test_manager->getBaseURL() . '?list=' . $test_manager->normalizeTestsPath($path) . '">More tests</a></p>';
}

function list_tests($path, &$test_manager, &$global_group)
{
  $current_group = $test_manager->getCaseByPath($path, $global_group);

  echo '<p><a href="' . $test_manager->getBaseURL() . '?list=' . $test_manager->normalizeTestsPath($path) . '/..">One level up</a></p>';

  $test_manager->displayTestCases($current_group, $test_manager->normalizeTestsPath($path));
}  

echo debug :: parse_html_console();	
ob_end_flush();

?>