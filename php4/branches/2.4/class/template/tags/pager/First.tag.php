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
class PagerFirstTagInfo
{
  var $tag = 'pager:FIRST';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'pager_first_tag';
}

registerTag(new PagerFirstTagInfo());

/**
* Compile time component for "back to start" element of pager.
*/
class PagerFirstTag extends ServerComponentTag
{
  var $hide_for_current_page;

  function checkNestingLevel()
  {
    if ($this->findParentByClass('pager_first_tag'))
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
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->is_first()) {');

    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->set("href", ' . $parent->getComponentRefCode() . '->get_first_page_uri());');
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
      $code->writePhp('if (!' . $parent->getComponentRefCode() . '->is_first()) {');
    }

    parent::postGenerate($code);

    $code->writePhp('}');
  }

  function generateContents($code)
  {
    $parent = $this->findParentByClass('pager_navigator_tag');

    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->is_first()) {');

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