<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/


class tests_sys_params extends GroupTest 
{
    function tests_sys_params() 
    {
        $this->GroupTest('sys params tests');
        $this->addTestFile(TEST_CASES_DIR . '/test_sys_params.php');
    }
}
?>