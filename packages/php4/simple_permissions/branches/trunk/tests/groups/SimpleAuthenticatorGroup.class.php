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
class SimpleAuthenticatorGroup extends LimbGroupTest
{
  function SimpleAuthenticatorGroup()
  {
    parent :: LimbGroupTest('simple permissions authenticator tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/../cases/authenticator/');
  }
}
?>