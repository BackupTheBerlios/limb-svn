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
class request_state_tag_info
{
  public $tag = 'request_state';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'request_state_tag';
}

register_tag(new request_state_tag_info());

class request_state_tag extends control_tag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/request_state_component';
  }

  public function prepare()
  {
    $this->attributes['type'] = 'hidden';
  }

  public function get_rendered_tag()
  {
    return 'input';
  }

  public function pre_generate($code)
  {
    if(isset($this->attributes['attach_form_prefix']))
      $code->write_php($this->get_component_ref_code() . '->attach_form_prefix(true);');
    else
      $code->write_php($this->get_component_ref_code() . '->attach_form_prefix(false);');

    parent :: pre_generate($code);
  }
}

?>