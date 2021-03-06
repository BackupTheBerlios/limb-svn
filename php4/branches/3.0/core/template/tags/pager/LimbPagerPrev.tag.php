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
$taginfo =& new TagInfo('limb:pager:PREV', 'LimbPagerPrevTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbPagerPrevTag extends CompilerDirectiveTag
{
  var $hide_for_current_page;

  function checkNestingLevel()
  {
    if ($this->findParentByClass('LimbPagerPrevTag'))
    {
      $this->raiseCompilerError('BADSELFNESTING');
    }

    if (!$this->findParentByClass('LimbPagerNavigatorTag'))
    {
      $this->raiseCompilerError('MISSINGENCLOSURE');
    }
  }

  function preGenerate(&$code)
  {
    $this->hide_for_current_page = $this->getBoolAttribute('hide_for_current_page');

    $parent =& $this->findParentByClass('LimbPagerNavigatorTag');
    $code->writePhp('if (' . $parent->getComponentRefCode() . '->hasPrev()) {');

    parent :: preGenerate($code);

    $code->writePhp($this->getDataSourceRefCode() . '->set("href", ' .
                    $parent->getComponentRefCode() . '->getPageUri( ' .
                    $parent->getComponentRefCode() . '->getDisplayedPage() - 1 ));');

    if (!$this->hide_for_current_page)
    {
      $code->writePhp('}');
    }
  }

  function postGenerate(&$code)
  {
    if (!$this->hide_for_current_page)
    {
      $parent =& $this->findParentByClass('LimbPagerNavigatorTag');
      $code->writePhp('if (' . $parent->getComponentRefCode() . '->hasPrev()) {');
    }

    parent::postGenerate($code);

    $code->writePhp('}');
  }

  function generateContents(&$code)
  {
    $parent =& $this->findParentByClass('LimbPagerNavigatorTag');

    $code->writePhp('if (' . $parent->getComponentRefCode() . '->hasPrev()) {');

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>