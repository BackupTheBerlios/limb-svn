<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: TemplateTagsGroup.class.php 1011 2005-01-11 16:29:30Z pachanga $
*
***********************************************************************************/
class LocaleTagsGroup extends LimbGroupTest
{
  function LocaleTagsGroup()
  {
    parent :: LimbGroupTest('locale tags tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/template/tags/locale');
  }
}
?>