<?php

class tests_image_library extends GroupTest 
{
	function tests_image_library() 
	{
	  $this->GroupTest('Image library');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/image');
	}
}
?>