<?php

class tests_validation extends GroupTest 
{
	function tests_validation() 
	{
	  $this->GroupTest('validation tests');
	  
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/validation/rules');
	}
}
?>