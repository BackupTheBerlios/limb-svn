<?php

foreach($GLOBALS['tests'] as $test_name)
{
	require_once($test_name . '.php');
}

class tests_all extends GroupTest 
{
  function tests_all() 
  {
    $this->GroupTest('all tests');
    
		foreach($GLOBALS['tests'] as $test_name)
		{
			$this->addTestCase(new $test_name());
		}
  }
}

  
?>