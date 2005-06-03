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
require_once(LIMB_DIR . '/tests/lib/TestsTreeManager.class.php');

class TestRunner
{
  var $test_manager;

  function testRunner()
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

    $res = $this->test_manager->run($current_group, $this->_getReporter());

    $this->_displayAfterPerform($path, $root_group, $current_group);

    return $res;
  }

  function browse($path, &$root_group)
  {
    $current_group =& $this->test_manager->getCaseByPath($path, $root_group);

    $this->_displayBrowse($this->test_manager->normalizeTestsPath($path), $root_group, $current_group);
  }
}

?>