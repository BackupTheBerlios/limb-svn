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
class PagerNumberTagInfo
{
  var $tag = 'pager:NUMBER';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'pager_number_tag';
}

registerTag(new PagerNumberTagInfo());

class PagerNumberTag extends ServerComponentTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('pager_number_tag'))
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

  function generateContents($code)
  {
    $parent = $this->findParentByClass('pager_navigator_tag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->is_current_page()) {');

    $code->writePhp($this->getComponentRefCode() . '->set("href", ' . $parent->getComponentRefCode() . '->get_current_page_uri());');
    $code->writePhp($this->getComponentRefCode() . '->set("number", ' . $parent->getComponentRefCode() . '->get_page_number());');

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