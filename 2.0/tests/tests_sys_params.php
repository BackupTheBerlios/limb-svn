<?php

class tests_sys_params extends GroupTest 
{
    function tests_sys_params() 
    {
        $this->GroupTest('sys params tests');
        $this->addTestFile(TEST_CASES_DIR . '/test_sys_params.php');
    }
}
?>