<?php

class tests_tree extends GroupTest 
{
	function tests_tree() 
	{
	  $this->GroupTest('tree');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/tree');
	}
}
?>