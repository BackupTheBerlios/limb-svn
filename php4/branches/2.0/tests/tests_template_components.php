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


class tests_template_components extends GroupTest 
{
  function tests_template_components() 
  {
    $this->GroupTest('template components tests');
    $this->addTestFile(TEST_CASES_DIR . '/components/test_template_component.php');
    $this->addTestFile(TEST_CASES_DIR . '/components/test_actions_component.php');
    $this->addTestFile(TEST_CASES_DIR . '/components/test_metadata_component.php');
  }
}
?>