<?php

class tests_metadata extends GroupTest 
{
    function tests_metadata() 
    {
        $this->GroupTest('metadata tests');
        TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/metadata');
    }
}
?>