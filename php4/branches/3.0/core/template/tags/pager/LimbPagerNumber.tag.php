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
$taginfo =& new TagInfo('limb:pager:NUMBER', 'LimbPagerNumberTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbPagerNumberTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('LimbPagerNumberTag'))
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
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isDisplayedPage()) {');

    $code->writePhp($this->getDataSourceRefCode() . '->set("href", ' . $parent->getComponentRefCode() . '->getPageUri());');
    $code->writePhp($this->getDataSourceRefCode() . '->set("number", ' . $parent->getComponentRefCode() . '->getPage());');

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>