<?php

class tests_sys_params extends GroupTest 
{
    function tests_sys_params() 
    {
        $this->GroupTest('sys params tests');
        TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/sys_params');
    }
}
?>