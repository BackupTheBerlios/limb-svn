<?php

class tests_object_access extends GroupTest 
{
	function tests_object_access() 
	{
	  $this->GroupTest('object access tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/access');
	}
}
?>