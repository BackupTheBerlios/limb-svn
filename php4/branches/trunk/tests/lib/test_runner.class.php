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
require_once(LIMB_DIR . '/tests/lib/test_runner.class.php');
require_once(LIMB_DIR . '/tests/lib/test_manager.class.php');

class TestRunner
{
  var $test_manager;
  
  function TestRunner()
  {
    $this->test_manager =& new TestManager();
  }
  
  function &_getReporter()
  {
    die('abstract method');
  }
    
  function _displayPerform($path, &$root_group, &$current_group)
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
    
    $this->test_manager->run($current_group, $this->_getReporter());
    
    $this->_displayPerform($this->test_manager->normalizeTestsPath($path), $root_group, $current_group);
  }
  
  function browse($path, &$root_group)
  {
    $current_group =& $this->test_manager->getCaseByPath($path, $root_group);
    
    $this->_displayBrowse($this->test_manager->normalizeTestsPath($path), $root_group, $current_group);
  }  
}

?>