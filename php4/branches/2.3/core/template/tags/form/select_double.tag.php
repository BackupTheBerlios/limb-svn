<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: select.tag.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/

require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class select_double_tag_info
{
  var $tag = 'select_double';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'select_double_tag';
}

register_tag(new select_double_tag_info());

/**
* Compile time component for building runtime select components
*/
class select_double_tag extends control_tag
{
  var $runtime_component_path = '/core/template/components/form/select_double_component';

  function get_rendered_tag()
  {
    return 'select';
  }

  function generate_contents(&$code)
  {
    if(!isset($this->attribute['multiple']))
      $this->attributes['multiple'] = 1;

    $code->write_php($this->get_component_ref_code() . '->render_contents();');
    parent :: generate_contents($code);
  }

  function post_generate(&$code)
  {
    parent :: post_generate($code);
    $code->write_php($this->get_component_ref_code() . '->render_control();');
  }

}
?>