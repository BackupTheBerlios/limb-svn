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
class TabsLabelsTagInfo
{
  public $tag = 'tabs:labels';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'tabs_labels_tag';
}

registerTag(new TabsLabelsTagInfo());

class TabsLabelsTag extends CompilerDirectiveTag
{
  public function checkNestingLevel()
  {
    if (!$this->parent instanceof TabsTag)
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'tabs',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function preGenerate($code)
  {
    $tabulator_class = $this->parent->tabulator_class;
    $tab_class = $this->parent->tab_class;

    $code->writeHtml("
    <table width=100% border=0 cellspacing=0 cellpadding=0 {$tabulator_class}>
    <tr>
    <tr>
      <td {$tab_class}>&nbsp;</td>");
  }

  public function postGenerate($code)
  {
    $tab_class = $this->parent->tab_class;

    $code->writeHtml("<td class=tab width=100%>&nbsp;</td>
      </tr>
      </table>");
  }
}

?>