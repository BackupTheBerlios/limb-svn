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
class TabsContentsTagInfo
{
  var $tag = 'tabs:contents';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'tabs_contents_tag';
}

registerTag(new TabsContentsTagInfo());

class TabsContentsTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if (!is_a($this->parent, 'TabsTag'))
    {
      return new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'tabs',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function preGenerate($code)
  {
    $code->writeHtml("
      <table>
      <tr>
        <td height=100% valign=top>
    ");

    parent :: preGenerate($code);
  }

  function postGenerate($code)
  {
    $tab_class = $this->parent->tab_class;

    $code->writeHtml("
      </td>
    </tr>
    </table>
    ");

    parent :: postGenerate($code);
  }
}

?>