<?php

class tests_fetching extends GroupTest 
{
	function tests_fetching() 
	{
	  $this->GroupTest('fetch operations');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/fetching');
	}
}
?>