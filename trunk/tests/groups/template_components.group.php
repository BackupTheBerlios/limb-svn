<?php

class tests_template_components extends GroupTest 
{
  function tests_template_components() 
  {
    $this->GroupTest('template components tests');
    TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/components');
  }
}
?>