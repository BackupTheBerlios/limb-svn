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
class pager_separator_tag_info
{
  public $tag = 'pager:SEPARATOR';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'pager_separator_tag';
}

register_tag(new pager_separator_tag_info());

/**
* Compile time component for seperators in a Pager
*/
class pager_separator_tag extends silent_compiler_directive_tag
{
  public function check_nesting_level()
  {
    if ($this->find_parent_by_class('pager_separator_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    if (!$this->find_parent_by_class('pager_navigator_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'pager:navigator',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }
}

?>