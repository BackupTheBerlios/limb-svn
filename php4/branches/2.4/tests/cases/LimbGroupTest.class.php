<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class LimbGroupTest extends GroupTest
{
  var $tests_handles = array();

  function addTestCaseHandle(&$handle)
  {
    $this->tests_handles[] =& $handle;
  }

  function &getTestCasesHandles()
  {
    return $this->tests_handles;
  }
}

?>