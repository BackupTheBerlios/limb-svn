<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/system/Fs.class.php');
require_once(LIMB_DIR . '/core/PackagesInfo.class.php');

class TestFinder
{
  var $_test_case_extension = 'test.class.php';
  var $_test_group_extension = 'group.class.php';

  function &getTestCasesHandlesFromPackages()
  {
    $finder = new TestFinder();
    $info =& PackagesInfo :: instance();

    $packages = $info->getPackages();

    $handles = array();

    foreach($packages as $package)
    {
      $files = $finder->_getFileList($package['path'] . '/tests/', array(&$finder, '_isTestCaseFile'));

      $handles = array_merge($handles, $finder->_getTestCasesHandlesFromFilesList($files));
    }

    return $handles;
  }

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

    if(!preg_match_all("~class\s+?([^\s]+)\s+?extends\s+?[a-zA-Z_]+[T,t]est~", $file, $matches))
      return array();

    return $matches[1];
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
        $file_list[] = Fs :: cleanPath($file_path);
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
        $file_list[] = Fs :: cleanPath($file_path);
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
    return $extension == strtolower(substr($file, (0 - strlen($extension))));
  }
}
?>