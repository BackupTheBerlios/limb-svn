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
class PagerListTagInfo
{
  var $tag = 'pager:LIST';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'pager_list_tag';
}

registerTag(new PagerListTagInfo());

/**
* Compile time component for the iterable section of the pager
*/
class PagerListTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('pager_list_tag'))
    {
      return throw(new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }
    if (!$this->findParentByClass('pager_navigator_tag'))
    {
      return throw(new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'pager:navigator',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }
  }

  function preGenerate($code)
  {
    parent::preGenerate($code);

    $parent = $this->findParentByClass('pager_navigator_tag');
    $code->writePhp('if (' . $parent->getComponentRefCode() . '->next()) {');
  }

  function postGenerate($code)
  {
    $code->writePhp('}');

    $emptychild = $this->findChildByClass('grid_default_tag');
    if ($emptychild)
    {
      $code->writePhp(' else { ');
      $emptychild->generateNow($code);
      $code->writePhp('}');
    }
    parent::postGenerate($code);
  }

  function generateContents($code)
  {
    $sep_child = $this->findChildByClass('pager_separator_tag');
    $current_child = $this->findChildByClass('pager_current_tag');
    $number_child = $this->findChildByClass('pager_number_tag');
    $section_child = $this->findChildByClass('pager_section_tag');

    $parent = $this->findParentByClass('pager_navigator_tag');

    $code->writePhp('do { ');

    if ($sep_child)
    {
      $code->writePhp('if (');
      $code->writePhp($parent->getComponentRefCode() . '->show_separator');
      $code->writePhp('&& (');
      $code->writePhp($parent->getComponentRefCode() . '->is_current_page() ||');
      $code->writePhp($parent->getComponentRefCode() . '->is_display_page()');
      $code->writePhp(')) {');
      $sep_child->generateNow($code);
      $code->writePhp('}');
      $code->writePhp($parent->getComponentRefCode() . '->show_separator = true;');
    }

    $code->writePhp('if (' . $parent->getComponentRefCode() . '->is_display_page()) {');

    $code->writePhp('if (!(' . $parent->getComponentRefCode() . '->is_first() && ' . $parent->getComponentRefCode() . '->is_last())) {');

    if ($number_child)
      $number_child->generate($code);

    if($current_child)
      $current_child->generate($code);

    $code->writePhp('}');

    $code->writePhp('}else{');

    $code->writePhp('if (' . $parent->getComponentRefCode() . '->has_section_changed()) {');

    if($section_child)
      $section_child->generate($code);

    $code->writePhp('}');

    $code->writePhp($parent->getComponentRefCode() . '->show_separator = false;');
    $code->writePhp('}');

    $code->writePhp('} while (' . $parent->getComponentRefCode() . '->next());');
  }
}

?>