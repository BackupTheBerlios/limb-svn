<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: StatsGroup.class.php 1075 2005-01-29 15:50:12Z pachanga $
*
***********************************************************************************/
class StatsRegistersGroup extends LimbGroupTest
{
  function StatsRegistersGroup()
  {
    parent :: LimbGroupTest('stats registers tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(dirname(__FILE__) . '/registers/');
  }
}
?>