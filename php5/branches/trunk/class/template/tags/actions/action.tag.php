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
class action_tag_info
{
  public $tag = 'actions:ITEM';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'action_tag';
}

register_tag(new action_tag_info());

/**
* Compile time component for items (rows) in the list
*/
class action_tag extends compiler_directive_tag
{
  public function check_nesting_level()
  {
    if (!$this->parent instanceof actions_tag)
    {
      throw new WactException('wrong parent tag',
          array('tag' => $this->tag,
          'parent_class' => get_class($this->parent),
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function generate_contents($code)
  {
    $code->write_php('do { ');

    parent::generate_contents($code);

    $code->write_php('} while (' . $this->get_dataspace_ref_code() . '->next());');
  }
}

?>