<?php

class tests_system extends GroupTest 
{
	function tests_system() 
	{
	  $this->GroupTest('system tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/system');
	}
}
?>