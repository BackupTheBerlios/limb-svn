<?php

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