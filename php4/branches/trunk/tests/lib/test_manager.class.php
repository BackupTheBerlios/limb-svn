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
require_once(LIMB_DIR . '/class/lib/system/fs.class.php');

class TestManager
{
	var $_test_case_extension = '_test.class.php';
	var $_test_group_extension = '_group.class.php';

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
	
	function run(&$test, &$reporter)
	{
    if(is_a($test, 'LimbGroupTest'))
  	  $this->_fillTestGroup($test);
	  
	  $test->run($reporter);
	}
	
	function &getCaseByPath($tests_path, &$group)
	{
	  $path_array = $this->_parseTestsPath($tests_path);
	  
	  if($path_array === false)
	    return $group;
	  
	  return $this->_getCaseByPathRecursive($path_array, $group);
	}
	
	function _parseTestsPath($tests_path)
	{
	  $path_array = explode('/', $tests_path);
	  
	  if(isset($path_array[0]) && $path_array[0] == '')
	    array_shift($path_array);
	    
	  $new_array = array();
	  foreach($path_array as $item)
	  {
	    if($item == '..')
	      array_pop($new_array);
	    else
	      $new_array[] = $item;
	  }
	
	  return $new_array;
	}
	
	function normalizeTestsPath($tests_path)
	{
	  $path_array = $this->_parseTestsPath($tests_path);
	  
	  return implode('/', $path_array);
	}
	
	function &_getCaseByPathRecursive($path_array, &$group)
	{
	  $test_cases =& $group->getTestCasesHandles();

    $case_index = array_shift($path_array);
    
    if(!isset($test_cases[$case_index]))
      return $group;
    
    $test_case =& $test_cases[$case_index];
    resolve_handle($test_case);
	  
	  if(sizeof($path_array) > 0)
	    return $this->_getCaseByPathRecursive($path_array, $test_case);
	  else
	    return $test_case;
	}
	
	function _fillTestGroup(&$group)
	{
	  $test_cases =& $group->getTestCasesHandles();
	  foreach(array_keys($test_cases) as $key)
	  {
	    resolve_handle($test_cases[$key]);
	    
	    if(is_a($test_cases[$key], 'LimbGroupTest'))
	      $this->_fillTestGroup($test_cases[$key]);
      
	    $group->addTestCase($test_cases[$key]);
	  }
	}

	function &getTestCasesHandlesFromDirectory($directory)
	{
	  $manager = new TestManager();
		$files = $manager->_getRecursiveFileList($directory,
			array(&$manager, '_isTestCaseFile'));
		
		return $manager->_getTestCasesHandlesFromFilesList($files);
	} 
	
	function _getTestCasesHandlesFromFilesList($files)
	{
		$cases_handles = array();
		foreach($files as $file)
		{
		  $class_names = $this->_getTestClassNames($file);
		  
		  foreach($class_names as $class_name)
		    array_push($cases_handles, $file . '|' . $class_name);
		}
		
		return $cases_handles;	
	}

	function &_getTestClassNames($test_file)
	{
		$file = implode("\n", file($test_file));
		
		if(!preg_match_all("~class\s+?([^\s]+)\s+?extends\s+?[a-zA-Z_]+[T,t]est~", $file, $matches))
		  return array();
		
		return $matches[1];
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
		return ($this->_hasExpectedExtension($file, $this->_test_case_extension) || 
		        $this->_hasExpectedExtension($file, $this->_test_group_extension));
	} 

	function _hasExpectedExtension($file, $extension)
	{
		return $extension ==
		strtolower(substr($file, (0 - strlen($extension))));
	} 
} 
?>