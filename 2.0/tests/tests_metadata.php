<?php

class tests_metadata extends GroupTest 
{
    function tests_metadata() 
    {
        $this->GroupTest('metadata tests');
        $this->addTestFile(TEST_CASES_DIR . '/test_save_metadata.php');
    }
}
?>