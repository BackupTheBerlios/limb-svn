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
class PagerPrevTagInfo
{
  var $tag = 'pager:PREV';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'pager_prev_tag';
}

registerTag(new PagerPrevTagInfo());

class PagerPrevTag extends ServerComponentTag
{
  var $hide_for_current_page;

  function checkNestingLevel()
  {
    if ($this->findParentByClass('pager_prev_tag'))
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
    $this->hide_for_current_page = array_key_exists('hide_for_current_page', $this->attributes);

    $parent = $this->findParentByClass('pager_navigator_tag');
    $code->writePhp('if (' . $parent->getComponentRefCode() . '->has_prev()) {');

    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->set("href", ' . $parent->getComponentRefCode() . '->get_prev_page_uri());');
    if (!$this->hide_for_current_page)
    {
      $code->writePhp('}');
    }
  }

  function postGenerate($code)
  {
    if (!$this->hide_for_current_page)
    {
      $parent = $this->findParentByClass('pager_navigator_tag');
      $code->writePhp('if (' . $parent->getComponentRefCode() . '->has_prev()) {');
    }
    parent::postGenerate($code);

    $code->writePhp('}');
  }

  function generateContents($code)
  {
    $parent = $this->findParentByClass('pager_navigator_tag');

    $code->writePhp('if (' . $parent->getComponentRefCode() . '->has_prev()) {');

    parent :: generateContents($code);

    $code->writePhp('}');
  }

  function getDataspace()
  {
    return $this;
  }

  function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>