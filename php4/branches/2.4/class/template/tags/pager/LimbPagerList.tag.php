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
$taginfo =& new TagInfo('limb:pager:LIST', 'LimbPagerListTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbPagerListTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('LimbPagerListTag'))
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
    parent::preGenerate($code);

    $parent =& $this->findParentByClass('LimbPagerNavigatorTag');
    $code->writePhp('while (' . $parent->getComponentRefCode() . '->isValid()) {');
  }

  function postGenerate(&$code)
  {
    $code->writePhp('}');

    parent::postGenerate($code);
  }

  function generateContents(&$code)
  {
    $sep_child = $this->findChildByClass('LimbPagerSeparatorTag');
    $current_child = $this->findChildByClass('LimbPagerDisplayedTag');
    $number_child = $this->findChildByClass('LimbPagerNumberTag');
    $section_child = $this->findChildByClass('LimbPagerSectionTag');

    $parent = $this->findParentByClass('LimbPagerNavigatorTag');

    $code->writePhp('if (' . $parent->getComponentRefCode() . '->isDisplayedSection()) {');

    $code->writePhp('if (!(' . $parent->getComponentRefCode() . '->isFirst() && ' .
                               $parent->getComponentRefCode() . '->isLast())) {');

    if ($number_child)
      $number_child->generate($code);

    if($current_child)
      $current_child->generate($code);


    $code->writePhp('}');

    $code->writePhp($parent->getComponentRefCode() . '->nextPage();');

    if ($sep_child)
    {
      $code->writePhp('if (');
      $code->writePhp($parent->getComponentRefCode() . '->isValid()');
      $code->writePhp(') {');
      $sep_child->generateNow($code);
      $code->writePhp('}');
    }

    $code->writePhp('}else{');

    if($section_child)
      $section_child->generate($code);

    $code->writePhp($parent->getComponentRefCode() . '->nextSection();');

    $code->writePhp('}');
  }
}

?>