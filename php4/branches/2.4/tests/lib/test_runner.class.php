<?php
require_once(LIMB_DIR . '/tests/lib/tests_tree_manager.class.php');

class TestRunner
{
  var $test_manager;

  function TestRunner()
  {
    $this->test_manager = new TestsTreeManager();
  }

  function &_getReporter()
  {
    die('abstract method');
  }

  function _displayBeforeRun($path, &$root_group, &$current_group)
  {
    die('abstract method');
  }

  function _displayAfterRun($path, &$root_group, &$current_group)
  {
    die('abstract method');
  }

  function _displayBrowse($path, &$root_group, &$current_group)
  {
    die('abstract method');
  }

  function run()
  {
    die('abstract method');
  }

  function perform($path, &$root_group)
  {
    $current_group =& $this->test_manager->getCaseByPath($path, $root_group);

    $path = $this->test_manager->normalizeTestsPath($path);

    $this->_displayBeforePerform($path, $root_group, $current_group);

    $this->test_manager->run($current_group, $this->_getReporter());

    $this->_displayAfterPerform($path, $root_group, $current_group);
  }

  function browse($path, &$root_group)
  {
    $current_group =& $this->test_manager->getCaseByPath($path, $root_group);

    $this->_displayBrowse($this->test_manager->normalizeTestsPath($path), $root_group, $current_group);
  }
}

?>