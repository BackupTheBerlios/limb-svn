<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class color_picker_tag_info
{
  var $tag = 'color_picker';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'color_picker_tag';
}

register_tag(new color_picker_tag_info());

class color_picker_tag extends control_tag
{
  var $runtime_component_path = '/core/template/components/form/color_picker_component';

  function get_rendered_tag()
  {
    return 'input';
  }

  function pre_generate(&$code)
  {
    $code->write_php($this->get_component_ref_code() . '->init_color_picker();');

    parent :: pre_generate($code);
  }

  function generate_contents(&$code)
  {
    parent :: generate_contents($code);

    $code->write_php($this->get_component_ref_code() . '->render_color_picker();');
  }
}

?>