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
require_once(LIMB_DIR . '/core/lib/system/fs.class.php');

class TestFinder
{
  var $_test_case_extension = 'test.class.php';
  var $_test_group_extension = 'group.class.php';

  function &getTestCasesHandlesFromDirectory($directory)
  {
    $finder = new TestFinder();
    $files = $finder->_getFileList($directory, array(&$finder, '_isTestCaseFile'));

    return $finder->_getTestCasesHandlesFromFilesList($files);
  }

  function &getTestCasesHandlesFromDirectoryRecursive($directory)
  {
    $finder = new TestFinder();
    $files = $finder->_getRecursiveFileList($directory, array(&$finder, '_isTestCaseFile'));

    return $finder->_getTestCasesHandlesFromFilesList($files);
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

    $excludes = array();
    if(preg_match_all('~SimpleTestOptions\s?::\s?ignore\((\'|")([^\'"]+)(\'|")\)~', $file, $matches))
      $excludes = $matches[2];

    if(!preg_match_all("~class\s+?([^\s]+)\s+?extends\s+.*test~i", $file, $matches))
      return array();

    return array_diff($matches[1], $excludes);
  }

  function &_getFileList($directory, $file_test_function)
  {
    if(!is_dir($directory))
      return array();

    $dh = opendir($directory);

    $file_list = array();
    while ($file = readdir($dh))
    {
      $file_path = $directory . '/' . $file;

      if (0 === strpos($file, '.'))
        continue;

      if ($file_test_function[0]->$file_test_function[1]($file))
      {
        $file_list[] = fs :: clean_path($file_path);
      }
    }
    closedir($dh);
    return $file_list;
  }

  function &_getRecursiveFileList($directory, $file_test_function)
  {
    if(!is_dir($directory))
      return array();

    $dh = opendir($directory);

    $file_list = array();
    while ($file = readdir($dh))
    {
      $file_path = $directory . '/' . $file;

      if (0 === strpos($file, '.'))
        continue;

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