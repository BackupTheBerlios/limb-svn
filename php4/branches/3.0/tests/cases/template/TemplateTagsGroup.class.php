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
class TemplateTagsGroup extends LimbGroupTest
{
  function TemplateTagsGroup()
  {
    parent :: LimbGroupTest(__FILE__);
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/template/tags');
  }
}
?>