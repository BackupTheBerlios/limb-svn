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

require_once(LIMB_DIR . '/tests/lib/test_manager.php');

if (!include_once(SIMPLE_TEST . 'reporter.php'))
{
	error('runtime', 'LIBRARY_REQUIRED', array('library' => 'Simple Test',
			'path' => SIMPLE_TEST));
} 

if(isset($_GET['all']))
{
	TestManager::runAllTests($_GET['all'], new HTMLReporter);
	
	echo "<p><a href='" . $_SERVER['PHP_SELF'] . "'>Run more tests</a></p>";
	
	echo debug :: parse_html_console();	
	ob_end_flush();
	exit(0);
}

if (isset($_GET['group']))
{
	TestManager::runGroupTest(ucfirst($_GET['group']),
		$_GET['group'],
		new HTMLReporter());

	echo "<p><a href='" . $_SERVER['PHP_SELF'] . "'>Run more tests</a></p>";
	echo debug :: parse_html_console();
	ob_end_flush();
	exit(0);
} 

if (isset($_GET['case']))
{
	TestManager::runTestCase($_GET['case'], new HTMLReporter());
	echo "<p><a href='" . $_SERVER['PHP_SELF'] . "?show=cases'>Run more tests</a></p>";
	echo debug :: parse_html_console();
	ob_end_flush();
	exit(0);
} 

echo "<h1>Unit Test Suite</h1>\n";
echo "<p><a href='" . $_SERVER['PHP_SELF'] . "'>Test groups</a>";
echo " || <a href='" . $_SERVER['PHP_SELF'] . "?show=cases'>Test cases</a></p>";

if (isset($_GET['show']) && $_GET['show'] == 'cases')
{
	echo HTMLTestManager::getGroupTestList(LIMB_DIR . '/tests/cases');
	echo HTMLTestManager::getGroupTestList(PROJECT_DIR . '/tests/cases');
} 
else
{
	/* no group specified, so list them all */
	echo HTMLTestManager::getGroupTestList(LIMB_DIR . '/tests/groups');
	echo HTMLTestManager::getGroupTestList(PROJECT_DIR . '/tests/groups');
} 

ob_end_flush();

?>