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
  public $tag = 'pager:FIRST';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'pager_first_tag';
}

registerTag(new PagerFirstTagInfo());

/**
* Compile time component for "back to start" element of pager.
*/
class PagerFirstTag extends ServerComponentTag
{
  protected $hide_for_current_page;

  public function checkNestingLevel()
  {
    if ($this->findParentByClass('pager_first_tag'))
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

  public function preGenerate($code)
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

  public function postGenerate($code)
  {
    if (!$this->hide_for_current_page)
    {
      $parent = $this->findParentByClass('pager_navigator_tag');
      $code->writePhp('if (!' . $parent->getComponentRefCode() . '->is_first()) {');
    }

    parent::postGenerate($code);

    $code->writePhp('}');
  }

  public function generateContents($code)
  {
    $parent = $this->findParentByClass('pager_navigator_tag');

    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->is_first()) {');

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