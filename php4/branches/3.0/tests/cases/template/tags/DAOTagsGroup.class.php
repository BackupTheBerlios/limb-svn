<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LocaleTagsGroup.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
class DAOTagsGroup extends LimbGroupTest
{
  function DAOTagsGroup()
  {
    parent :: LimbGroupTest('DAO tags tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/template/tags/DAO');
  }
}
?>