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
$taginfo =& new TagInfo('limb:pager:FIRST', 'LimbPagerFirstTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbPagerFirstTag extends CompilerDirectiveTag
{
  var $hide_for_current_page;

  function checkNestingLevel()
  {
    if ($this->findParentByClass('LimbPagerFirstTag'))
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

    $parent = $this->findParentByClass('LimbPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isFirst()) {');

    parent::preGenerate($code);

    $code->writePhp($this->getDataSourceRefCode() . '->set("href", ' . $parent->getComponentRefCode() . '->getFirstPageUri());');
    if (!$this->hide_for_current_page)
    {
      $code->writePhp('}');
    }
  }

  function postGenerate(&$code)
  {
    if (!$this->hide_for_current_page)
    {
      $parent = $this->findParentByClass('LimbPagerNavigatorTag');
      $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isFirst()) {');
    }

    parent::postGenerate(&$code);

    $code->writePhp('}');
  }

  function generateContents(&$code)
  {
    $parent = $this->findParentByClass('LimbPagerNavigatorTag');

    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isFirst()) {');

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>