<?php
class tests_compiler extends GroupTest 
{
  function tests_compiler() 
  {
    $this->GroupTest('compiler tests');
    $this->addTestFile(TEST_CASES_DIR . '/test_compiler_codewriter.php');
  }
}
?>