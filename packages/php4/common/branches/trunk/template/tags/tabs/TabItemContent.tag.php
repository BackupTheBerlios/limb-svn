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
class TabItemContentTagInfo
{
  public $tag = 'tab_item:content';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'tab_item_content_tag';
}

registerTag(new TabItemContentTagInfo());

class TabItemContentTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if (!is_a($this->parent, 'TabsContentsTag'))
    {
      return new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'tabs:contents',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function preParse()
  {
    if (!isset($this->attributes['tab_id']))
    {
      return new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'id',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    if(!in_array($this->attributes['tab_id'], $this->parent->parent->tabs))
    {
      return new WactException('invalid attribute value',
          array('tag' => $this->tag,
          'attribute' => 'tab_id',
          'description' => 'tab_id not declared in <tab_item:label> tag',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function preGenerate($code)
  {
    $id = $this->attributes['tab_id'];

    $code->writeHtml("<div id='{$id}_content'>");

    parent :: preGenerate($code);
  }

  function postGenerate($code)
  {
    $code->writeHtml("</div>");

    parent :: postGenerate($code);
  }
}

?>