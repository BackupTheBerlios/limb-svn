<?php 

class LimbCLIReporter extends TextReporter 
{
  function paintCaseEnd($test_name)
  {
    parent :: paintCaseEnd($test_name);
    
    print $this->getTestCaseProgress() . "/" . $this->getTestCaseCount() . " done({$test_name})\n";
  }
}
?>