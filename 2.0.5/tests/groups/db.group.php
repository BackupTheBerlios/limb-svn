<?php

class tests_db extends GroupTest 
{
	function tests_db() 
	{
	  $this->GroupTest('db tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/db');
	}
}
?>