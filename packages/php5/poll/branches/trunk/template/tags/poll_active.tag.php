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

class poll_active_tag_info
{
  public $tag = 'poll_active';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'poll_active_tag';
}

register_tag(new poll_active_tag_info());

/**
* The parent compile time component for lists
*/
class poll_active_tag extends server_component_tag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/poll_component';
  }

  public function pre_generate($code)
  {
    parent::pre_generate($code);

    $code->write_php($this->get_component_ref_code() . '->prepare();');
  }

  public function generate_contents($code)
  {
    $code->write_php('if (' . $this->get_component_ref_code() . '->poll_exists()) {');
    parent :: generate_contents($code);
    $code->write_php('}');
  }

  public function get_dataspace()
  {
    return $this;
  }

  public function get_dataspace_ref_code()
  {
    return $this->get_component_ref_code();
  }
}

?>