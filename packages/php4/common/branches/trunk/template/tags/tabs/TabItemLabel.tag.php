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
class TabItemLabelTagInfo
{
  public $tag = 'tab_item:label';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'tab_item_label_tag';
}

registerTag(new TabItemLabelTagInfo());

class TabItemLabelTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if (!$this->parent instanceof TabsLabelsTag)
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'tabs:labels',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function preParse()
  {
    if (!isset($this->attributes['tab_id']) ||  !$this->attributes['tab_id'])
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'tab_id',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    $this->parent->parent->tabs[] = $this->attributes['tab_id'];

    return PARSER_REQUIRE_PARSING;
  }

  function preGenerate($code)
  {
    $id = $this->attributes['tab_id'];

    $code->writeHtml("<td id={$id}>
          <table border='0' cellspacing='0' cellpadding='0' style='height:100%'>
          <tr>
            <td nowrap {$this->parent->parent->tab_class}><a href='JavaScript:void(0);'>");

    parent :: preGenerate($code);
  }

  function postGenerate($code)
  {
    $code->writeHtml("</a></td>
          </tr>
          </table>
        </td>
    ");

    parent :: postGenerate($code);
  }
}

?>