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
class DbGroup extends LimbGroupTest
{
  function dbGroup()
  {
    $this->limbGroupTest('db tests');
  }

  function getTestCasesHandles()
  {
    $handles = array();
    $handles = TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/db');

    $db_type = getIniOption('common.ini', 'type', 'DB');

    $handles = array_merge(
      $handles,
      TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/db/' . $db_type)
    );

    return $handles;
  }

}
?>