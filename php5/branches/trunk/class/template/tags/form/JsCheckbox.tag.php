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
require_once(LIMB_DIR . '/class/template/tags/form/control_tag.class.php');

class js_checkbox_tag_info
{
  public $tag = 'js_checkbox';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'js_checkbox_tag';
}

register_tag(new js_checkbox_tag_info());

class js_checkbox_tag extends control_tag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/js_checkbox_component';
  }

  public function get_rendered_tag()
  {
    return 'input';
  }

  public function pre_generate($code)
  {
    $this->attributes['type'] = 'hidden';

    parent :: pre_generate($code);
  }

  public function generate_contents($code)
  {
    parent :: generate_contents($code);

    $code->write_php($this->get_component_ref_code() . '->render_js_checkbox();');
  }
}

?>