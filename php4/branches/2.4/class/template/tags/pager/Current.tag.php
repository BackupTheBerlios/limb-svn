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
class PagerCurrentTagInfo
{
  public $tag = 'pager:current';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'pager_current_tag';
}

registerTag(new PagerCurrentTagInfo());

class PagerCurrentTag extends ServerComponentTag
{
  public function checkNestingLevel()
  {
    if ($this->findParentByClass('pager_current_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    if (!$this->findParentByClass('pager_navigator_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'pager:navigator',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function generateContents($code)
  {
    $parent = $this->findParentByClass('pager_navigator_tag');

    $parent = $this->findParentByClass('pager_navigator_tag');

    $code->writePhp('if (' . $parent->getComponentRefCode() . '->is_current_page()) {');

    $code->writePhp($this->getComponentRefCode() . '->set("href", ' . $parent->getComponentRefCode() . '->get_current_page_uri());');
    $code->writePhp($this->getComponentRefCode() . '->set("number", ' . $parent->getComponentRefCode() . '->get_page_number());');

    parent :: generateContents($code);

    $code->writePhp('}');
  }

  public function getDataspace()
  {
    return $this;
  }

  public function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>