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
$taginfo =& new TagInfo('limb:pager:SECTION', 'LimbPagerSectionTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbPagerSectionTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('LimbPagerSectionTag'))
    {
      $this->raiseCompilerError('BADSELFNESTING');
    }

    if (!$this->findParentByClass('LimbPagerListTag'))
    {
      $this->raiseCompilerError('MISSINGENCLOSURE');
    }
  }

  function generateContents(&$code)
  {
    $parent =& $this->findParentByClass('LimbPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isDisplayedSection()) {');

    $code->writePhp($this->getDataSourceRefCode() . '->set("href", ' . $parent->getComponentRefCode() . '->getSectionUri());');
    $code->writePhp($this->getDataSourceRefCode() . '->set("number_begin", ' . $parent->getComponentRefCode() . '->getSectionBeginPage());');
    $code->writePhp($this->getDataSourceRefCode() . '->set("number_end", ' . $parent->getComponentRefCode() . '->getSectionEndPage());');

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>