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


class sys_params_group extends GroupTest 
{
    function sys_params_group() 
    {
        $this->GroupTest('sys params tests');
        TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/sys_params');
    }
}
?>