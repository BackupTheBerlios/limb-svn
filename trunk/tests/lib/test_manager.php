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
require_once(LIMB_DIR . '/core/lib/system/fs.class.php');

class TestManager
{
	var $_testcase_extension = '_test.class.php';
	var $_grouptest_extension = '_group.class.php';

	function TestManager()
	{
		$this->_installSimpleTest();
	} 

	function _installSimpleTest()
	{
		require_once(SIMPLE_TEST . 'unit_tester.php');
		require_once(SIMPLE_TEST . 'web_tester.php');
		require_once(SIMPLE_TEST . 'mock_objects.php');
	} 

	function runAllTests($directory, &$reporter)
	{
		$manager = &new TestManager();
		
		$test_groups = &$manager->_getTestGroupFileList($directory);
		$test = &new GroupTest('All Tests');
		
		foreach ($test_groups as $test_group_file)
		{
			include_once($test_group_file);
			
			$group_classes = $manager->_getGroupTestClassNames($test_group_file);
			
			foreach($group_classes as $group_class)
				$test->addTestCase(new $group_class());
		}
		
		$test->run($reporter);
	} 

	function runTestCase($testcase_file, &$reporter)
	{
		$manager = &new TestManager();

		if (! file_exists($testcase_file))
		{
			trigger_error("Test case {$testcase_file} cannot be found",
				E_USER_ERROR);
		} 

		$test = &new GroupTest("Individual test case: " . $testcase_file);
		$test->addTestFile($testcase_file);
		$test->run($reporter);
	} 

	function runGroupTest($group_test_name, $group_test_directory, &$reporter)
	{
		$manager = &new TestManager();

		if (! file_exists($group_test_name))
		{
			trigger_error("Group test {$group_test_name} cannot be found at {$file_path}",
				E_USER_ERROR);
		} 

		require_once($group_test_name);
		$test = &new GroupTest($group_test_name . ' group test');
		
		foreach ($manager->_getGroupTestClassNames($group_test_name) as $group_test)
		{
			$test->addTestCase(new $group_test());
		} 
		$test->run($reporter);
	} 

	function addTestCasesFromDirectory(&$group_test, $directory)
	{
		$manager = &new TestManager();
		$test_cases = &$manager->_getTestFileList($directory);
		sort($test_cases);
		foreach ($test_cases as $test_case)
		{			
			$group_test->addTestFile($test_case);
		} 
	} 

	function &getTestCaseList($directory)
	{
		$manager = &new TestManager();
		return $manager->_getTestFileList($directory);
	} 

	function &_getTestFileList($directory)
	{
		return $this->_getRecursiveFileList($directory,
			array(&$this, '_isTestCaseFile'));
	} 

	function &getGroupTestList($directory)
	{
		$manager = &new TestManager();
		return $manager->_getTestGroupList($directory);
	} 

	function &_getTestGroupFileList($directory)
	{
		return $this->_getRecursiveFileList($directory,
			array(&$this, '_isTestGroupFile'));
	} 

	function &_getTestGroupList($directory)
	{
		$file_list = &$this->_getTestGroupFileList($directory);
		sort($file_list);
		return $file_list;
	} 

	function &_getGroupTestClassNames($grouptest_file)
	{
		$file = implode("\n", file($grouptest_file));
		preg_match("~lass\s+?(.*)\s+?extends GroupTest~", $file, $matches);
		if (! empty($matches))
		{
			unset($matches[0]);
			return $matches;
		} 
		else
		{
			return array();
		} 
	} 

	function &_getRecursiveFileList($directory, $file_test_function)
	{
		$dh = opendir($directory);
		if (! is_resource($dh))
		{
			trigger_error("Couldn't open {$directory}", E_USER_ERROR);
		} 

		$file_list = array();
		while ($file = readdir($dh))
		{
			$file_path = $directory . DIRECTORY_SEPARATOR . $file;

			if (0 === strpos($file, '.')) continue;

			if (is_dir($file_path))
			{
				$file_list =
				array_merge($file_list,
					$this->_getRecursiveFileList(
						$file_path,
						$file_test_function));
			} 
			if ($file_test_function[0]->$file_test_function[1]($file))
			{
				$file_list[] = fs :: clean_path($file_path);
			} 
		} 
		closedir($dh);
		return $file_list;
	} 

	function _isTestCaseFile($file)
	{
		return $this->_hasExpectedExtension($file, $this->_testcase_extension);
	} 

	function _isTestGroupFile($file)
	{
		return $this->_hasExpectedExtension($file, $this->_grouptest_extension);
	} 

	function _hasExpectedExtension($file, $extension)
	{
		return $extension ==
		strtolower(substr($file, (0 - strlen($extension))));
	} 
} 

class CLITestManager extends TestManager
{
	function &getGroupTestList($directory)
	{
		if(!file_exists($directory))
			return '';

		$manager = &new CLITestManager();
		$group_tests = &$manager->_getTestGroupList($directory);

		$buffer = "Available grouptests in {$directory}:\n";
		foreach ($group_tests as $group_test)
		{
			$buffer .= "  " . $group_test . "\n";
		} 
		return $buffer . "\n";
	} 

	function &getTestCaseList($directory)
	{
		if(!file_exists($directory))
			return '';

		$manager = &new CLITestManager();
		$test_cases = &$manager->_getTestFileList($directory);

		$buffer = "Available test cases:\n";
		foreach ($test_cases as $test_case_file => $test_case)
		{
			$buffer .= "  " . $test_case_file . "\n";
		} 
		return $buffer . "\n";
	} 
} 

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

	function &getGroupTestList($directory)
	{
		if(!file_exists($directory))
			return '';
		
		$directory = fs :: clean_path($directory);
		$manager = &new HTMLTestManager();
		$group_tests = &$manager->_getTestGroupList($directory);
		
		if (1 > count($group_tests))
		{
			return "<p>No test groups in '{$directory}'</p>";
		} 
		$buffer = "<p>Available test groups in '{$directory}':</p>\n<ul>";
		
		foreach ($group_tests as $group_test)
		{
			$output_group_test = str_replace($directory, '', $group_test);
			
			$buffer .= "<li><a href='" . $manager->getBaseURL() . "?group={$group_test}'>" . $output_group_test . "</a></li>\n";
		} 
		$buffer .= "</ul>\n";
		
		return $buffer .= "<br><a href='" . $manager->getBaseURL() . "?all={$directory}'>All group tests from this directory</a>\n";
	} 

	function &getTestCaseList($directory)
	{
		if(!file_exists($directory))
			return '';

		$directory = fs :: clean_path($directory);
		$manager = &new HTMLTestManager();
		$testcases = &$manager->_getTestFileList($directory);

		if (1 > count($testcases))
		{
			return "<p>No test cases set up!</p>";
		} 
		$buffer = "<p>Available test cases:</p>\n<ul>";
		foreach ($testcases as $testcase_file => $testcase)
		{
			$buffer .= "<li><a href='" . $manager->getBaseURL() . "?case=" . urlencode($testcase_file) . "'>" . $testcase . "</a></li>\n";
		} 
		return $buffer . "</ul>\n";
	} 
} 

?>