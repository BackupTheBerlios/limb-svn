<?php

class tests_util extends GroupTest 
{
	function tests_util() 
	{
	  $this->GroupTest('util tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/util');
	}
}
?>