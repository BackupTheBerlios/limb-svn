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
class order_tag_info
{
  public $tag = 'order';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'order_tag';
}

register_tag(new order_tag_info());

class order_tag extends server_component_tag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/order_component';
  }

  public function pre_generate($code)
  {
    parent::pre_generate($code);

    $code->write_php($this->get_component_ref_code() . "->import(" . $this->parent->get_dataspace_ref_code() . "->export());\n");

    $code->write_php($this->get_component_ref_code() . '->prepare();'."\n");
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