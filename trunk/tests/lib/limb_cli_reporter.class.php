<?php 

class LimbCLIReporter extends TextReporter 
{
  function paintCaseEnd($test_name)
  {
    parent :: paintCaseEnd($test_name);
    
    print $this->getTestCaseProgress() . " of " . $this->getTestCaseCount() . " test cases done...\n";
  }
}
?>