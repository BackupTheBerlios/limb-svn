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
class PagerSectionTagInfo
{
  var $tag = 'pager:section';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'pager_section_tag';
}

registerTag(new PagerSectionTagInfo());

class PagerSectionTag extends ServerComponentTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('pager_section_tag'))
    {
      return new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    if (!$this->findParentByClass('pager_navigator_tag'))
    {
      return new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'pager:navigator',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function generateContents($code)
  {
    $parent = $this->findParentByClass('pager_navigator_tag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->is_display_page()) {');

    $code->writePhp($this->getComponentRefCode() . '->set("href", ' . $parent->getComponentRefCode() . '->get_current_section_uri());');
    $code->writePhp($this->getComponentRefCode() . '->set("number_begin", ' . $parent->getComponentRefCode() . '->get_current_section_begin_number());');
    $code->writePhp($this->getComponentRefCode() . '->set("number_end", ' . $parent->getComponentRefCode() . '->get_current_section_end_number());');

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