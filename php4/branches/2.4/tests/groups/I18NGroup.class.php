<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CronGroup.class.php 921 2004-11-23 15:53:11Z pachanga $
*
***********************************************************************************/
class I18NGroup extends LimbGroupTest
{
  function I18NGroup()
  {
    parent :: LimbGroupTest('i18n tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/cases/i18n');
  }

}
?>