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

class TestsTreeManager
{
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
}
?>